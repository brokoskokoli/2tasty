<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\ImageFile;
use App\Entity\Recipe;
use App\Entity\RecipeTag;
use App\Events;
use App\Form\CommentType;
use App\Form\RecipeFilterType;
use App\Form\RecipeImportFromLinkType;
use App\Form\RecipeType;
use App\Form\Type\IngredientType;
use App\Repository\RecipeRepository;
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
 * @Route("/recipes")
 * @Security("has_role('ROLE_USER')")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class RecipesController extends AbstractController
{

    /**
     * Lists all Recipe entities.
     *
     * @Route("/", name="recipes_list_my")
     * @Security("has_role('ROLE_USER')")
     * @Method("GET")
     */
    public function listMyAction(RecipeRepository $recipes): Response
    {
        $authorRecipes = $recipes->getMyRecipes($this->getUser());

        return $this->render(
            'front/recipes/list.html.twig',
            [
                'recipes' => $authorRecipes,
                'user' => $this->getUser(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Recipe entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="recipes_edit")
     * @Security("is_granted('show', recipe)")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request,
                               RecipeService $recipeService,
                               Recipe $recipe,
                               IngredientService $ingredientService,
                               RecipeTagService $recipeTagService,
                               RecipeListService $recipeListService
    ): Response {
        $this->denyAccessUnlessGranted('edit', $recipe, 'Recipes can only be edited by their authors.');

        $form = $this->createForm(RecipeType::class, $recipe, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeService->saveRecipe($recipe);
            $this->addFlash('success', 'messages.recipe_modified');

            return $this->redirectToRoute('recipes_edit', ['id' => $recipe->getId()]);
        }

        return $this->render('front/recipes/edit.html.twig', [
            'recipe' => $recipe,
            'ingredientList' => $ingredientService->getAllNames(),
            'form' => $form->createView(),
            'recipeTags' => $recipeTagService->getAllNames(),
            'recipeLists' => $recipeListService->getAllForUser($this->getUser()),
        ]);
    }

    /**
     * Add a recipe to collected recipes to use them in lists etc.
     *
     * @Route("/{id}/add_to_collection", requirements={"id": "\d+"}, name="recipes_add_to_collection")
     * @Security("is_granted('show', recipe)")
     * @Method({"GET", "POST"})
     */
    public function addToCollectionAction(Request $request,
                                          RecipeService $recipeService,
                                          Recipe $recipe
    ): Response {

        if ($recipeService->addRecipeToUserCollection($recipe, $this->getUser())) {
            $this->addFlash('success', 'messages.recipe_added_to_collection');
        }

        return $this->redirectToRoute('recipes_show', ['slug' => $recipe->getSlug()]);
    }

    /**
     * Remove a recipe from collected recipes
     *
     * @Route("/{id}/remove_from_collection", requirements={"id": "\d+"}, name="recipes_remove_from_collection")
     * @Security("is_granted('show', recipe)")
     * @Method({"GET", "POST"})
     */
    public function removeFromCollectionAction(Request $request,
                                          RecipeService $recipeService,
                                          Recipe $recipe
    ): Response {

        if ($recipeService->removeRecipeFromUserCollection($recipe, $this->getUser())) {
            $this->addFlash('success', 'messages.recipe_removed_from_collection');
        }

        return $this->redirectToRoute('recipes_show', ['slug' => $recipe->getSlug()]);
    }

    /**
     * Creates a new Recipe entity.
     *
     * @Route("/new", name="recipes_new", defaults={"quick": false})
     * @Route("/new_quick", name="recipes_new_quick", defaults={"quick": true})
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     * @param Request $request
     * @param RecipeService $recipeService
     * @param IngredientService $ingredientService
     * @param RecipeTagService $recipeTagService
     * @param bool $quick
     * @return Response
     */
    public function newAction(Request $request, RecipeService $recipeService, IngredientService $ingredientService, RecipeTagService $recipeTagService, $quick = false): Response
    {
        $recipe = new Recipe();
        $recipe->setAuthor($this->getUser());
        if ($quick) {
            $recipe->addImage(new ImageFile());
        }

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(RecipeType::class, $recipe, ['user' => $this->getUser(), 'recipe' => $recipe])
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {
            $recipeService->saveRecipe($recipe);

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'messages.recipe_created');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                if ($quick) {
                    return $this->redirectToRoute('recipes_new_quick');
                } else {
                    return $this->redirectToRoute('recipes_new');
                }
            }

            return $this->redirectToRoute('recipes_edit', ['id' => $recipe->getId()]);
        }

        return $this->render('front/recipes/new.html.twig', [
            'ingredientList' => $ingredientService->getAllNames(),
            'recipe' => $recipe,
            'form' => $form->createView(),
            'recipeTags' => $recipeTagService->getAllNames(),
            'quick' => $quick,
        ]);
    }


    /**
     * Deletes a Recipe entity.
     *
     * @Route("/{id}/delete", name="recipes_delete")
     * @Method("POST")
     * @Security("is_granted('delete', recipe)")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function delete(Request $request, RecipeService $recipeService, Recipe $recipe): Response
    {
        /*if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_recipe_index');
        }*/

        $ok = $recipeService->deleteRecipe($recipe);

        if ($ok) {
            $this->addFlash('success', 'recipe.deleted_successfully');
        }

        return $this->redirectToRoute('recipes_list_my');
    }



    /**
     * @Route("/search", name="recipes_search")
     * @Method("GET")
     */
    public function search(Request $request, RecipeRepository $recipes): Response
    {

        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);
        $foundRecipes = $recipes->findBySearchQuery($query, $limit);

        $results = [];
        foreach ($foundRecipes as $recipe) {
            $results[] = [
                'title' => htmlspecialchars($recipe->getTitle()),
                'date' => $recipe->getCreatedAt()->format('M d, Y'),
                'author' => htmlspecialchars($recipe->getAuthor()->getFullName()),
                'summary' => htmlspecialchars($recipe->getSummary()),
                'url' => $this->generateUrl('recipes_show', ['slug' => $recipe->getSlug()]),
            ];
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->render(
                'front/recipes/search.html.twig',
                [
                    'results' => $results,
                ]
            );
        }

        return $this->json($results);
    }

    /**
     * Finds and displays a Recipe entity.
     *
     * @Route("/recipe/{id}", requirements={"id": "\d+"}, name="recipes_show_id")
     * @Route("/recipe/{slug}", name="recipes_show")
     * @Method("GET")
     */
    public function show(IngredientService $ingredientService, Recipe $recipe): Response
    {
        return $this->render('front/recipes/show.html.twig', [
            'recipe' => $recipe,
            'user' => $this->getUser(),
        ]);
    }


    /**
     * @Route("/new_recipes", defaults={"page": "1", "format"="html"}, name="recipes_news")
     * @Route("/new_recipes/rss.xml", defaults={"page": "1", "format"="xml"}, name="blog_rss")
     * @Route("/new_recipes/{page}", defaults={"page": "1", "format"="html"}, requirements={"page": "[1-9]\d*"}, name="recipes_news_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * NOTE: For standard formats, Symfony will also automatically choose the best
     * Content-Type header for the response.
     * See https://symfony.com/doc/current/quick_tour/the_controller.html#using-formats
     * @param RecipeService $recipeService
     * @param int $page
     * @param string $format
     * @return Response
     */
    public function index(RecipeService $recipeService, int $page = null, string $format = null): Response
    {
        $latestRecipes = $recipeService->getLatest($page);

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templating.html#template-suffix
        return $this->render('front/recipes/index.'.$format.'.twig', ['recipes' => $latestRecipes]);
    }

    /**
     * Download pdf with recipe
     *
     * @Route("/download_recipe_pdf/{slug}", name="recipes_recipe_download_pdf")
     * @Method("GET")
     */
    public function downloadPDFAction(PDFExportService $exportService, Recipe $recipe): Response
    {
        $exportService->generateRecipePDF($recipe);
        die;
    }

    /**
     * @Route("/recipe_of_the_day", name="recipes_recipe_recipe_of_the_day")
     * @Method("GET")
     */
    public function myRecipeOfTheDayAction(RecipeService $recipeService)
    {
        $recipe = $recipeService->getRandom([]);

        return $this->render('front/recipes/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    /**
     *
     * @Route("/filter_recipes", defaults={"page": "1", "format"="html"}, name="recipes_filter")
     * @Route("/filter_recipes/{page}", defaults={"page": "1", "format"="html"}, requirements={"page": "[1-9]\d*"}, name="recipes_filter_paginated")
     *
     * @param Request $request
     * @param RecipeService $recipeService
     * @return Response
     */
    public function filterAction(Request $request, RecipeService $recipeService, RecipeTagService $recipeTagService, int $page = null)
    {

        $form = $this->createForm(RecipeFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            if ($form->get('filter')->isClicked()) {
                $result = $recipeService->filterRecipes($page, $filters, $this->getUser());
            } else {
                $resultRecipe = $recipeService->randomRecipe($filters, $this->getUser());
            }
        }

        return $this->render('front/recipes/filter.html.twig', [
            'recipes' => $result ?? [],
            'recultRecipe' => $resultRecipe ?? null,
            'form' => $form->createView(),
            'recipeTags' => $recipeTagService->getAllNames(),
        ]);


    }


    /**
     * @Route("/import_from_link", name="recipes_import_from_link")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param RecipeService $recipeService
     * @param IngredientService $ingredientService
     * @param RecipeTagService $recipeTagService
     * @param bool $quick
     * @return Response
     */
    public function importFromLinkAction(Request $request, RecipeService $recipeService, IngredientService $ingredientService, RecipeTagService $recipeTagService, $quick = false): Response
    {
        $form = $this->createForm(RecipeImportFromLinkType::class);
        $form->handleRequest($request);
        $error = false;

        if ($form->isSubmitted() && $form->isValid()) {

            $filters = $form->getData();

            $recipe = $recipeService->createRecipeFromLink($filters['link'], $this->getUser());

            if ($recipe !== null) {
                $recipeService->saveRecipe($recipe);
                $this->addFlash('success', 'messages.recipe_created');
                return $this->redirectToRoute('recipes_edit', ['id' => $recipe->getId()]);
            }

            $error = true;
        }

        return $this->render('front/recipes/import_from_link.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
}
