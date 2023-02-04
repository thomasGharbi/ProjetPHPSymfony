<?php

namespace App\Controller\Security\Dashboard;


use App\Entity\User;
use App\Form\Security\Dashboard\DeleteCompanyType;
use App\Form\Security\Dashboard\DeleteUserType;
use App\Form\Security\Dashboard\UserDashboardType;
use App\Service\ConfirmIdentitySecurity;
use App\Service\DeleteUser;
use App\Service\SaveImages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class UserDashboardController extends AbstractController
{


    #[Route("/espace-utilisateur", name: "app_user_dashboard", defaults: ['public_access' => false], methods: ['GET', 'POST'])]
    public function userDashboard
    (
        Request                 $request,
        EntityManagerInterface  $manager,
        SaveImages              $saveImages,
        ConfirmIdentitySecurity $confirmIdentitySecurity,
        DeleteUser              $deleteUser,

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

        $userDatas = ['firstname' => $user->getFirstName(),
                      'name' => $user->getName(),
                      'username' => $user->getUsername(),
                      'email' => $user->getEmail(),
                      'phone' => $user->getPhone(),
                      'gender' => $user->getGender(),
                      'birth' => $user->getBirth()];

        $userDashboardForm = $this->createForm(UserDashboardType::class, $userDatas);
        $userDashboardForm->handleRequest($request);






        if ($userDashboardForm->isSubmitted() && $userDashboardForm->isValid() && $user->getIsVerified()) {

            //verifie si l'utisateur a modifier le HTML pour bypass la confirmation d'identité
            $confirmIdentitySecurity->checkIfBypassConfirmIdentity();



            $this->userUpdate($userDashboardForm, $saveImages, $user);

            $manager->flush();

            return $this->redirectToRoute('app_user_dashboard');

        }

        $deleteUserForm = $this->createForm(DeleteUserType::class);
        $deleteUserForm->handleRequest($request);

        if ($deleteUserForm->isSubmitted() && $deleteUserForm->isValid()) {
            $confirmIdentitySecurity->checkIfBypassConfirmIdentity();


            $deleteUser->deleteUser($user);



                $session = $this->get('session');
                $session = new Session();
                $session->invalidate();

                //dd($this->redirectToRoute('app_logout'));

            $this->addFlash('alert alert-primary', 'l\'utilisateur a été supprimé');

            return $this->redirectToRoute('app_logout');
        }


        $listCompanies = $this->getCompanies($user);


        return $this->render('security/dashboard/userDashboard.html.twig', ['userDashboardForm' => $userDashboardForm->createView(),'userDelete' => $deleteUserForm->createView() ,'isVerified' => $user->getIsVerified(), 'user' => $user, 'list_companies' => $listCompanies]);
    }


    /**
     * @param FormInterface<mixed> $formDashboard
     * @param User $user
     * @return void
     */
    public function userUpdate(FormInterface $formDashboard, SaveImages $saveImages, User $user): void
    {
        $profileImageEntered = $formDashboard->get('profile_image')->getData();




        if ($profileImageEntered) {

            $profileImage = $saveImages->formateAndSaveImage(
                $profileImageEntered,
                $this->getParameter('app.profile_image_directory'),
                $this->getParameter('app.profile_image_directory_render'));

            $user->setProfileImage($profileImage);


        }



        $formDashboard->get('email')->getData() ? $user->setEmail($formDashboard->get('email')->getData()) : null;
        $formDashboard->get('username')->getData() ? $user->setUsername($formDashboard->get('username')->getData()) : null;
        $user->setPhone($formDashboard->get('phone')->getData());
        $formDashboard->get('birth')->getData() ? $user->setBirth($formDashboard->get('birth')->getData()) : null;
        $formDashboard->get('gender')->getData() ? $user->setGender($formDashboard->get('gender')->getData()) : null;
        $formDashboard->get('firstname')->getData() ? $user->setFirstName($formDashboard->get('firstname')->getData()) : null;
        $formDashboard->get('name')->getData() ? $user->setName($formDashboard->get('name')->getData()) : null;


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
                'profile_image' => $value->getProfileImage(),
                'uuid_of_company' => $value->getUuid()];


        }

        return $listCompanies;
    }


}
