<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\ImageFile;
use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\RecipeTag;
use App\Entity\User;
use App\Events;
use App\Form\CommentType;
use App\Form\RecipeFilterType;
use App\Form\RecipeImportFromLinkType;
use App\Form\RecipeListType;
use App\Form\RecipeType;
use App\Form\Type\IngredientType;
use App\Repository\RecipeListRepository;
use App\Repository\RecipeRepository;
use App\Security\RecipeListVoter;
use App\Service\DatabaseTranslationLoaderService;
use App\Service\IngredientService;
use App\Service\PDFExportService;
use App\Service\RecipeListService;
use App\Service\RecipeService;
use App\Service\RecipeTagService;
use App\Utils\Slugger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;

/**
 * Controller used to manage blog contents in the public part of the site.
 *
 * @Route("/recipelists")
 * @Security("is_granted('ROLE_USER')")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class RecipeListsController extends AbstractController
{

    /**
     * Lists all RecipeLists.
     *
     * @Route("/", name="recipelists_list")
     * @Security("is_granted('ROLE_USER')")
     * @Method("GET")
     * @param RecipeListRepository $recipes
     * @return Response
     */
    public function listMyAction(RecipeListService $recipeListService): Response
    {
        $authorRecipeLists = $recipeListService->getAllForUser($this->getUser());

        return $this->render(
            'front/recipeLists/list.html.twig',
            [
                'recipeLists' => $authorRecipeLists
            ]
        );
    }

    /**
     * Displays a form to edit an existing Recipe entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="recipelists_edit")
     * @Route("/new", name="recipelists_new")
     * @Method({"GET", "POST"})
     */
    public function editAction(
        Request $request,
        RecipeListService $recipeListService,
        RecipeList $recipeList = null
    ): Response {
        if ($recipeList === null) {
            $recipeList = new RecipeList();
        }

        $this->denyAccessUnlessGranted(RecipeListVoter::EDIT, $recipeList, 'RecipeLists can only be edited by their authors.');

        $form = $this->createForm(RecipeListType::class, $recipeList, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeListService->saveRecipeList($recipeList, $this->getUser());
            $this->addFlash('success', 'messages.recipe_list_modified');
            if ($form->get('submit_active')->isClicked()) {
                if ($recipeListService->makeActive($recipeList, $this->getUser())){
                    $this->addFlash('success', 'messages.recipelist_make_activ');
                }
            }

            return $this->redirectToRoute('recipelists_edit', ['id' => $recipeList->getId()]);
        }

        return $this->render(
            'front/recipeLists/edit.html.twig', [
            'recipeList' => $recipeList,
            'form' => $form->createView(),
        ]
        );
    }

    /**
     * Deletes a Recipe entity.
     *
     * @Route("/{id}/delete", name="recipelists_delete")
     * @Method("POST")
     */
    public function delete(Request $request, RecipeListService $recipeListService, RecipeList $recipeList): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('recipes_list_my');
        }

        if ($recipeList->getAuthor()->getId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('recipelists_list');
        }

        $recipeListService->deleteRecipeList($recipeList);
        $this->addFlash('success', 'messages.recipelist_deleted');

        return $this->redirectToRoute('recipelists_list');
    }


    /**
     * @Route("/display/{id}", requirements={"id": "\d+"}, name="recipelists_show_id")
     * @Route("/display/{slug}", name="recipelists_show")
     * @Method("GET")
     */
    public function show(RecipeList $recipeList): Response
    {
        return $this->render(
            'front/recipeLists/show.html.twig', [
            'recipeList' => $recipeList,
        ]
        );
    }


    /**
     * Set a recipelist as active to add recipes
     *
     * @Route("/make_active/{id}", requirements={"id": "\d+"}, name="recipelists_make_active")
     * @Method("GET")
     */
    public function setActiveAction(Request $request, RecipeListService $recipeListService, RecipeList $recipeList): Response
    {
        if (!$this->getUser() instanceof User) {
            return $this->redirectToRoute('recipes_list_my');
        }

        if ($recipeListService->makeActive($recipeList, $this->getUser())){
            $this->addFlash('success', 'messages.recipelist_make_activ');
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Set a recipelist as active to add recipes
     *
     * @Route("/remove_active/{id}", requirements={"id": "\d+"}, name="recipelists_remove_active")
     * @Method("GET")
     */
    public function unsetActiveAction(Request $request, RecipeListService $recipeListService, RecipeList $recipeList): Response
    {
        if (!$this->getUser() instanceof User) {
            return $this->redirectToRoute('recipes_list_my');
        }

        if ($recipeListService->removeActive($this->getUser())) {
            $this->addFlash('success', 'messages.recipelist_remove_activ');
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     *
     * @Route("/add_recipe_to_active/{id}", requirements={"id": "\d+"}, name="recipelists_add_to_active")
     * @Method("GET")
     */
    public function addRecipeToActiveRecipeList(Request $request, RecipeListService $recipeListService, RecipeService $recipeService, Recipe $recipe)
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('recipes_list_my');
        }

        if (!$user->getActiveRecipeList()) {
            return $this->redirectToRoute('recipes_list_my');
        }

        $recipeService->addRecipeToUserCollection($recipe, $user);

        if ($recipeListService->addRecipeToList($user->getActiveRecipeList(), $recipe)) {
            $this->addFlash('success', 'messages.recipe_added');
        }
        return $this->redirect($request->headers->get('referer'));
    }

}
