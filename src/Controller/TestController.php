<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function index(SessionInterface $session): Response
    {



        return $this->render('test.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
