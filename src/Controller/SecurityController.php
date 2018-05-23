<?php


namespace App\Controller;

use App\Form\Type\RequestPasswordFormType;
use App\Form\Type\ResetPasswordFormType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller used to manage the application security.
 * See https://symfony.com/doc/current/cookbook/security/form_login_setup.html.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="security_index")
     * @return RedirectResponse
     */
    public function indexAction() : RedirectResponse
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security_login');
        }

        return $this->redirectToRoute('recipes_list_my');
    }
    
    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $helper
     * @return Response
     */
    public function loginAction(AuthenticationUtils $helper): Response
    {
        return $this->render('front/security/login.html.twig', [
            // last username entered by the user (if any)
            'last_username' => $helper->getLastUsername(),
            // last authentication error (if any)
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/forgot-password", name="security_request_password")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param UserService $userService
     * @return RedirectResponse|Response
     */
    public function requestPasswordAction(Request $request, UserService $userService)
    {
        $form = $this->createForm(RequestPasswordFormType::class);
        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid()) {
            $email = $form['email']->getData();
            $userService->requestPassword($email);
            $this->addFlash('success', 'messages.forgot_password_mail_send');
            return $this->redirectToRoute('security_login');
        }

        return $this->render('front/security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/reset-password/{token}", name="security_reset_password")
     * @Method({"GET", "POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $token
     * @return Response|RedirectResponse
     */
    public function resetPasswordAction(Request $request, UserService $userService, $token)
    {
        $user = $userService->getUserFromLostPasswordToken($token);

        if (empty($user)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid()) {
            $userService->resetPassword($user, $form['plainPassword']->getData());
            $this->addFlash('success', 'messages.reset_password');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('front/security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in app/config/security.yml
     *
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
