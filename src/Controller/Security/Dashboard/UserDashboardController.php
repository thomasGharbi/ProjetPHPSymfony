<?php

namespace App\Controller\Security\Dashboard;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserDashboardController extends AbstractController
{


    /**
     * @Route("/espace-utilisateur", name="app_user_dashboard")
     */
    public function userDashboard(): RedirectResponse
    {
        return $this->redirectToRoute('app_login');
    }
}
