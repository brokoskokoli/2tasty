<?php

namespace App\Service;

use App\Entity\User;
use App\Helper\SecurityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{

    /**
     * @var MailerService
     */
    private $mailerService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param MailerService $mailerService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder,
        MailerService $mailerService
    ) {
        $this->em = $entityManager;
        $this->encoder = $encoder;
        $this->mailerService = $mailerService;
    }

    /**
     * Demande de mot de passe oublié
     *
     * @param $email
     * @return bool
     */
    public function requestPassword($email)
    {
        $user = $this->em->getRepository('App:User')->findOneBy(['email' => $email]);
        if ($user instanceof User) {
            $user->setForgotPasswordToken(SecurityHelper::generatePasswordResetToken());

            $this->em->persist($user);
            $this->em->flush();

            $this->mailerService->sendUserResetPasswordEmail($user);
            return true;
        }

        return false;
    }

    public function getUserFromLostPasswordToken($token)
    {
        return $this->em->getRepository('App:User')->findOneBy(['forgotPasswordToken' => $token]);
    }


    public function resetPassword(User $user, $password)
    {
        $password = $this->encoder->encodePassword($user, $password);

        $user->setPassword($password);
        $user->setPlainPassword(null);
        $user->clearForgotPasswordToken();

        $this->em->persist($user);
        $this->em->flush();
    }

    public function saveUser(User $user)
    {
        // todo : rename slug of recipes

        foreach ($user->getIngredientDisplayPreferenceOverrides() as &$override) {
            $override->setAuthor($user);
        }

        $this->em->persist($user);
        $this->em->flush();
    }
}
