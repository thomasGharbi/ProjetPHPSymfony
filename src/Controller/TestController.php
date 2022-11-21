<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\VisitorRepository;
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
    public function index(SessionInterface $session, EntityManagerInterface $entityManager, VisitorRepository $visitorRepository): Response
    {

        $user  = $this->getUser();

        if($user instanceof User)
        {
            $visitorRepository->deleteUserOfVisit($user);
        }





        return $this->render('test.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
