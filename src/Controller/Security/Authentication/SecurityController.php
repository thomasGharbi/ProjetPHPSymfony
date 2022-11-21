<?php



namespace App\Controller\Security\Authentication;

use App\Entity\User;
use App\Service\AddVisitor;
use DateTimeImmutable;
use App\Form\Security\Authentication\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class SecurityController extends AbstractController
{



    #[Route('/Connexion', name: 'app_login' , defaults: ['public_access' => true], methods: ['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session, Request $request, AddVisitor $addVisitor): Response
    {

        $addVisitor->addPointToVisitor('visitor_login', 1);
         if ($this->getUser()) {
           // return $this->redirectToRoute('app_login');
         }

        // get the login error if there is one


        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/Authentication/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    #[Route('/Deconnexion', name: 'app_logout' , defaults: ['public_access' => false], methods: ['GET','POST'])]
    public function logout(): void
    {
        throw new \LogicException('');
    }
}
