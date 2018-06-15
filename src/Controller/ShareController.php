<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Events;
use App\Form\CommentType;
use App\Form\ShareMailType;
use App\Service\MailerService;
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
 * @Route("/share")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class ShareController extends AbstractController
{
    /**
     * @Route("/email", name="share_email")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function shareEmailAction(Request $request, EventDispatcherInterface $eventDispatcher, MailerService $mailerService): Response
    {

        $form = $this->createForm(ShareMailType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $mailerService->sendShareEmail($data, $this->getUser());
            return $this->redirect($data['link']);
        }

        return $this->render('front/share/_share_form.html.twig', [
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
    public function shareForm(?string $link, ?string $title): Response
    {
        $form = $this->createForm(ShareMailType::class);
        $form->get('title')->setData($title);
        $form->get('link')->setData($link);

        return $this->render('front/share/_share_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
