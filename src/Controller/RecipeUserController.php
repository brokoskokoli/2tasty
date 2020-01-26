<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\ImageFile;
use App\Entity\Recipe;
use App\Entity\RecipeTag;
use App\Events;
use App\Form\CommentType;
use App\Form\RecipeDisplaySettingsType;
use App\Form\RecipeFilterType;
use App\Form\RecipeImportFromLinkType;
use App\Form\RecipeType;
use App\Form\Type\IngredientType;
use App\Repository\RecipeRepository;
use App\Service\DatabaseTranslationLoaderService;
use App\Service\IngredientService;
use App\Service\PDFExportService;
use App\Service\RecipeListService;
use App\Service\RecipeRatingService;
use App\Service\RecipeService;
use App\Service\RecipeTagService;
use App\Service\RecipeUserService;
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
 * @Route("/recipe_user")
 *
 */
class RecipeUserController extends AbstractController
{

    /**
     * @Route("/recipe_of_the_day", name="recipe_user_recipe_of_the_day")
     * @Security("is_granted('ROLE_USER')")
     * @Method("GET")
     */
    public function myRecipeOfTheDayAction(RecipeUserService $recipeUserService)
    {
        $recipe = $recipeUserService->getRecipeProposal($this->getUser());

        return $this->render('front/recipes/dish_of_the_day_show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * @Route("/{id}/cook_now", requirements={"id": "\d+"}, name="recipe_user_cook_now")
     * @Security("is_granted('ROLE_USER')")
     * @Method("GET")
     */
    public function cookNowAction(RecipeUserService $recipeUserService, Recipe $recipe): Response
    {
        $recipeUserService->userCooksNow($recipe, $this->getUser());

        return $this->redirectToRoute('recipes_show_id', ['id' => $recipe->getId()]);
    }

    /**
     * @Route("/{id}/cook_soon", requirements={"id": "\d+"}, name="recipe_user_cook_soon")
     * @Security("is_granted('ROLE_USER')")
     * @Method("GET")
     */
    public function cookSoonAction(RecipeUserService $recipeUserService, Recipe $recipe): Response
    {
        $recipeUserService->userCooksSoon($recipe, $this->getUser());

        return $this->redirectToRoute('recipe_user_recipe_of_the_day');
    }

    /**
     * @Route("/{id}/cooked_recently", requirements={"id": "\d+"}, name="recipe_user_cooked_recently")
     * @Security("is_granted('ROLE_USER')")
     * @Method("GET")
     */
    public function cookedRecentlyAction(RecipeUserService $recipeUserService, Recipe $recipe): Response
    {
        $recipeUserService->userCooksNow($recipe, $this->getUser());

        return $this->redirectToRoute('recipe_user_recipe_of_the_day');
    }

}
