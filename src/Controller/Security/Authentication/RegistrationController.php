<?php

namespace App\Controller\Security\Authentication;

use App\Entity\User;
use App\Service\Captcha;
use App\Service\SendEmail;
use App\Form\Security\Authentication\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'registration')]
    public function index(): Response
    {
        return $this->render('registration/Authentication/index.html.twig',
            [
                'controller_name' => 'RegistrationController',
            ]);
    }

    /**
     * @Route("/Inscription", name="app_registration")
     */
    public function registration(
        EntityManagerInterface  $manager,
        Request                 $request,
        SendEmail               $sendEmail,
        TokenGeneratorInterface $tokenGenerator,
        ValidatorInterface      $validator,
        Captcha                 $Captcha

    ): Response
    {


        $user = new User;
        $registrationForm = $this->createForm(RegistrationType::class, $user);

        $registrationForm->handleRequest($request);

        if($registrationForm->isSubmitted() && $registrationForm->isValid()) {


            if(!$Captcha->isHCaptchaValid())
            {
                return  $this->redirectToRoute('app_login');
            }
            $registrationToken = $tokenGenerator->generateToken();




            // converti les valeur du nom et prènom en "nocase".
            $creadentialsFormated = ['name' => strtolower($registrationForm->getData()->getName()),
                'firstName' => strtolower($registrationForm->getData()->getFirstName())];


            $user->setName($creadentialsFormated['name'])
                 ->setFirstName($creadentialsFormated['firstName'])
                 ->setRegistrationToken($registrationToken)
                 ->setCreatedAt(new \DateTimeImmutable('NOW'))
                 ->setRoles($user->getRoles())
                 ->setAccountMustBeVerifiedBefore(new \DateTimeImmutable('+ 3days'));
                 // Le Hachage du mot de passe est effectué via le UserPasswordHasherListener.

            $manager->persist($user);

            $manager->flush();

            $sendEmail->send([
                'recipient' => $user->getEmail(),
                'subject' => "vérification de votre compte",
                'html_template' => "Security/Authentication/email/registrationEmail.html.twig",
                'context' => [
                    'userID' => $user->getId(),
                    'registrationToken' => $registrationToken,
                    'tokenDuration' => $user->getAccountMustBeVerifiedBefore()?->format('d/m/Y à H:i')
                ]
            ]);
            return $this->redirectToRoute('app_login');
        }
        $error = $validator->validate($user);

        return $this->render('security/Authentication/registration.html.twig', ['registrationForm' => $registrationForm->createView(), 'error' => $error ?? null]);


    }


    /**
     * @Route("/Verification/{id<\d+>}/{token}", name="app_verify_account", methods={"GET"})
     */
    public function verifyAccount(
        User                   $user,
        string                 $token,
        EntityManagerInterface $entityManager): RedirectResponse
    {

        $isRequestedInTime = (new \DateTimeImmutable('NOW') > $user->getAccountMustBeVerifiedBefore()) ? false : true;

        if ($user->getRegistrationToken() === null || $user->getRegistrationToken() !== $token || !$isRequestedInTime)
        {
            throw new AccessDeniedException();
        }

        $user->setIsVerified(true)
             ->setAccountVerifiedAt(new \DateTimeImmutable('now'))
             ->setRegistrationToken(null);


        $entityManager->flush();

        $this->addFlash('success', 'Votre compte vient d\'étre confirmé confirmé');

        return $this->redirectToRoute('app_login');

    }
}
