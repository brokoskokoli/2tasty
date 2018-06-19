<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\User;
use App\Events;
use App\Form\CommentType;
use App\Form\ShareMailType;
use App\Service\MailerService;
use App\Service\RecipeListService;
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
 * @Route("/layout")
 */
class LayoutController extends AbstractController
{

    public function sidebar(RecipeListService $recipeListService, ?User $user): Response
    {
        $lists = $recipeListService->getAllForUser($user, true);

        return $this->render('front/recipeLists/_user_recipelists_sidebar.html.twig', [
            'recipelists' => $lists,
        ]);
    }


}
