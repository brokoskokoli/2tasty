<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\ImageFile;
use App\Entity\Recipe;
use App\Entity\RecipeTag;
use App\Entity\User;
use App\Events;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Form\Type\IngredientType;
use App\Form\UserType;
use App\Repository\RecipeRepository;
use App\Service\DatabaseTranslationLoaderService;
use App\Service\IngredientService;
use App\Service\PDFExportService;
use App\Service\RecipeService;
use App\Service\RecipeTagService;
use App\Service\UserService;
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
 * @Route("/user")
 * @Security("has_role('ROLE_USER')")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/my_profile", name="user_my_profile")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, UserService $userService): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userService->saveUser($user);
            $this->addFlash('success', 'messages.profile_modified');

            return $this->redirectToRoute('user_my_profile');
        }

        return $this->render('front/user/my_profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/profile/{username}", name="user_profile")
     * @Method({"GET", "POST"})
     */
    public function profileAction(Request $request, UserService $userService, User $user = null): Response
    {
        return $this->render('front/user/profile.html.twig', [
            'user' => $user,
        ]);
    }

}
