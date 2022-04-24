<?php

namespace App\Controller\Security\Authentication;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Security\Authentication\ModifyPasswordType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ModifyPasswordController extends AbstractController
{


    /**
     * @Route("/Verification_mot_de_passe/{id<\d+>}/{token}", name="app_check_modify_password", methods={"GET"})
     */
    public function checkModifyPassword(
        ?User            $user,
        SessionInterface $session,
        string           $token
    ): RedirectResponse
    {

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $isRequestedInTime = (new DateTimeImmutable('NOW') > $user->getAccountMustBeVerifiedBefore()) ? false : true;

        if ($user->getForgotPasswordToken() === null || $user->getForgotPasswordToken() !== $token || !$isRequestedInTime) {
            throw new AccessDeniedException();
        }

        $user->setForgotPasswordToken(null);
        $session->set('authorization-modify-password', true); //validation pour modifier le mot de passe
        $session->set('modify-password-user-email', $user->getEmail());

        return $this->redirectToRoute('app_modify_password');
    }


    /**
     * @Route("/Modification_mot_de_passe", name="app_modify_password")
     */
    public function ModifyPassword(

        Request                $request, SessionInterface $session,
        EntityManagerInterface $manager,
        UserRepository         $userRepository): Response
    {

        $user = $this->getUser();

        if (!$user) {
            $userEmail = $session->get('modify-password-user-email');
            $user = $userRepository->findOneBy(['email' => $userEmail]);
        }


        if (!$session->get('authorization-modify-password') || !$user) {


            throw new AccessDeniedException();


        }

        $modifyPasswordForm = $this->createForm(ModifyPasswordType::class);
        $modifyPasswordForm->handleRequest($request);

        if ($modifyPasswordForm->isSubmitted() && $modifyPasswordForm->isValid() && $user instanceof User) {

            $password = $modifyPasswordForm->get('modifyPassword')->getData();

            // Le Hachage est effectué via le UserPasswordHasherListener
            $user->setPassword($password)
                 ->setPasswordModifiedAt(new DateTimeImmutable('NOW'))
                 ->setForgotPasswordToken(null);

            $session->set('autorization-modify-password', null);
            $session->set('modify-password-user-email', null);

            //$manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_login');

        }


        return $this->render('security/Authentication/modifyPassword.html.twig', ['modifyPasswordForm' => $modifyPasswordForm->createView()]);
    }
}