<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Events;
use App\Form\CommentType;
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
 * Controller used to manage blog contents in the public part of the site.
 *
 * @Route("/comments")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class CommentsController extends AbstractController
{
    /**
     * @Route("/{recipeSlug}/new", name="comment_new")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("recipe", options={"mapping": {"recipeSlug": "slug"}})
     *
     * NOTE: The ParamConverter mapping is required because the route parameter
     * (recipeSlug) doesn't match any of the Doctrine entity properties (slug).
     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter
     */
    public function commentNew(Request $request, Recipe $recipe, EventDispatcherInterface $eventDispatcher): Response
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $recipe->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            // When triggering an event, you can optionally pass some information.
            // For simple applications, use the GenericEvent object provided by Symfony
            // to pass some PHP variables. For more complex applications, define your
            // own event object classes.
            // See https://symfony.com/doc/current/components/event_dispatcher/generic_event.html
            $event = new GenericEvent($comment);

            // When an event is dispatched, Symfony notifies it to all the listeners
            // and subscribers registered to it. Listeners can modify the information
            // passed in the event and they can even modify the execution flow, so
            // there's no guarantee that the rest of this controller will be executed.
            // See https://symfony.com/doc/current/components/event_dispatcher.html
            $eventDispatcher->dispatch(Events::COMMENT_CREATED, $event);

            return $this->redirectToRoute('blog_recipe', ['slug' => $recipe->getSlug()]);
        }

        return $this->render('front/recipes/comment_form_error.html.twig', [
            'recipe' => $recipe,
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
    public function commentForm(Recipe $recipe): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('front/recipes/_comment_form.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }


}
