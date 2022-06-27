<?php

namespace App\Controller\Security\Dashboard;


use App\Entity\User;
use App\Form\Security\Dashboard\UserDashboardType;
use App\Service\ConfirmIdentitySecurity;
use App\Utils\SaveImages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserDashboardController extends AbstractController
{


    #[Route("/espace-utilisateur", name: "app_user_dashboard")]
    public function userDashboard
    (
        Request                 $request,
        EntityManagerInterface  $manager,
        SaveImages              $saveImages,
        ConfirmIdentitySecurity $confirmIdentitySecurity,
    ): Response
    {


        /**
         * @var null|User $user
         */
        $user = $this->getUser();


        //Vérifie si l'utilisateur est présent et
        //le déconnecte et renvoie un message flash si plusieurs tentatives de confirmation d'identité échoué.
        $user = $confirmIdentitySecurity->checkConfirmIdentityRequest($user);

        if (!($user instanceof User)) {
            return $this->redirectToRoute('app_logout');
        }


        $userDashboardForm = $this->createForm(UserDashboardType::class, $user);
        $userDashboardForm->handleRequest($request);


        if ($userDashboardForm->isSubmitted() && $userDashboardForm->isValid() && $user->getIsVerified()) {
            //verifie si l'utisateur a modifier le HTML pour bypass la confirmation d'dentité
            $confirmIdentitySecurity->checkIfBypassConfirmIdentity();


            $this->userUpdate($userDashboardForm, $saveImages, $user);

            $manager->flush();

            return $this->redirectToRoute('app_user_dashboard');

        }


        $listCompanies = $this->getCompanies($user);

        return $this->render('security/dashboard/userDashboard.html.twig', ['userDashboardForm' => $userDashboardForm->createView(), 'isVerified' => $user->getIsVerified(), 'profil_image' => $user->getProfilImage(), 'list_companies' => $listCompanies]);
    }


    /**
     * @param FormInterface<mixed> $formDashboard
     * @param User $user
     * @return void
     */
    public function userUpdate(FormInterface $formDashboard, SaveImages $saveImages, User $user): void
    {
        $profilImageEntered = $formDashboard->get('profil_image')->getData();


        if ($profilImageEntered) {

            $profilImage = $saveImages->formateAndSaveImage(
                $profilImageEntered,
                $this->getParameter('app.profil_image_directory'),
                $this->getParameter('app.profil_image_directory_render'));

            $user->setProfilImage($profilImage);


        }

        $formDashboard->get('email')->getData() ? $user->setEmail($formDashboard->get('email')->getData()) : null;
        $formDashboard->get('username')->getData() ? $user->setUsername($formDashboard->get('username')->getData()) : null;


    }

    /**
     * @param User $user
     * @return array<mixed>
     */
    public function getCompanies(User $user): array
    {
        $companies = $user->getCompanies();

        $listCompanies = [];

        foreach ($companies as $value) {

            $listCompanies[] = ['name_of_company' => $value->getNameOfCompany(),
                'uuid_of_company' => $value->getUuid()];


        }

        return $listCompanies;
    }


}
