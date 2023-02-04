<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\VisitorRepository;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test', defaults: ['public_access' => true] ,methods: ['GET','POST'])]
    public function index(SendEmail               $sendEmail,): Response
    {
        $user = $this->getUser();
        $sendEmail->send([
            'recipient' => $user->getEmail(),
            'subject' => "vérification de votre compte",
            'html_template' => "email/registrationEmail.html.twig",
            'context' => [
                'userID' => $user->getId(),
                'registrationToken' => 'tokenTest',
                'tokenDuration' => $user->getCreatedAt()?->format('d/m/Y à H:i')
            ]
        ]);

        return $this->render('test.html.twig', [
            'controller_name' => 'TestController',
        ]);


    }
}
