<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\RecipeRating;
use App\Events;
use App\Form\CommentType;
use App\Form\RatingType;
use App\Form\ShareMailType;
use App\Service\MailerService;
use App\Service\RecipeRatingService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rating")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class RatingController extends AbstractController
{
    /**
     * @Route("/rate", name="rating_rate")
     * @Method("POST")
     * @Security("is_granted('ROLE_USER')")
     */
    public function rateAction(Request $request, EventDispatcherInterface $eventDispatcher, RecipeRatingService $recipeRatingService): Response
    {
        $rating = new RecipeRating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setAuthor($this->getUser());
            $recipeRatingService->saveRating($rating);

            return $this->redirectToRoute('recipes_show', ['slug' => $rating->getRecipe()->getSlug()]);
        }

        return $this->render('front/rating/_rate_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * This controller is called directly via the render() function in the
     * recipes/recipe_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Recipe is passed in and then turned into a Recipe object
     * automatically by the ParamConverter.
     */
    public function rateForm(RecipeRatingService $recipeRatingService, Recipe $recipe): Response
    {
        $ratingGlobal = $recipeRatingService->getRatingGlobal($recipe);
        $existingRating = $recipeRatingService->getRatingFromUser($recipe, $this->getUser());

        $form = $this->createForm(RatingType::class);
        $form->get('recipe')->setData($recipe);
        $form->get('rating')->setData(($existingRating?$existingRating->getRating():null));

        return $this->render('front/rating/_rate_form.html.twig', [
            'form' => $form->createView(),
            'ratingGlobal' => $ratingGlobal,
            'ratingUser' => $existingRating,
        ]);
    }


}
