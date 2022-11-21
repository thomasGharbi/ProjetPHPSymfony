<?php

namespace App\Controller\Security\Authentication;

use App\Service\SendEmail;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Security\Authentication\ForgotPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotPasswordController extends AbstractController
{


    #[Route('/mot-de-passe-oublie', name: 'app_forgot_password' , defaults: ['public_access' => true],methods: ['POST'])]
    public function forgotPassword(

        SendEmail               $sendEmail,
        TokenGeneratorInterface $tokenGeneratorInterface,
        Request                 $request,
        EntityManagerInterface  $manager,
        UserRepository          $userRepository


    ): Response
    {

        $forgotPasswordForm = $this->createForm(ForgotPasswordType::class);

        $forgotPasswordForm->handleRequest($request);

        if ($forgotPasswordForm->isSubmitted() && $forgotPasswordForm->isValid()) {

            $emailEntered = $forgotPasswordForm->get('email')->getData();

            $user = $userRepository->findOneBy(['email' => $emailEntered]);

            if ($user) {

                $forgotPasswordToken = $tokenGeneratorInterface->generateToken();

                $user->setForgotPasswordMustBeVerifiedBefore(new \DateTimeImmutable('+ 1days'))
                     ->setForgotPasswordRequestedAt(new \DateTimeImmutable('NOW'))
                     ->setForgotPasswordToken($forgotPasswordToken);


                $manager->flush();

                $sendEmail->send([
                    'recipient' => $emailEntered,
                    'subject' => "Mot de passe oublié",
                    'html_template' => "Security/Authentication/email/forgotPasswordEmail.html.twig",
                    'context' => [
                        'userID' => $user->getId(),
                        'forgotPasswordToken' => $forgotPasswordToken,
                        'tokenDuration' => $user->getforgotPasswordMustBeVerifiedBefore()?->format('d/m/Y à H:i')
                    ]
                ]);
            }

            $this->addFlash('success', "Un email de confirmation vous a été envoyé a $emailEntered ");


        }

        return $this->render('security/Authentication/forgotPassword.html.twig', ['forgotPasswordForm' => $forgotPasswordForm->createView()]);
    }
}
