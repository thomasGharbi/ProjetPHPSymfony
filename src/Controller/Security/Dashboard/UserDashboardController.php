<?php

namespace App\Controller\Security\Dashboard;


use App\Entity\User;
use App\Event\DOSEvent;
use App\Form\Security\UserDashboardType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserDashboardController extends AbstractController
{

    /**
     * @Route("/espace-utilisateur", name="app_user_dashboard")
     */
    public function userDashboard
    (
        Request                  $request,
        SluggerInterface         $slugger,
        SessionInterface         $session,
        EntityManagerInterface   $manager,
        EventDispatcherInterface $eventDispatcher,
        Security                 $security


    ): Response
    {


        $user = $this->getUser();

        //Vérifie si l'utilisateur est présent ou
        //le déconnecte et renvoie un message flash si plusieurs tentatives de confirmation d'identité échoué.
        if ($session->get('Password-Confirmation-Invalid') === 3 || !($user instanceof User)) {
            if ($user instanceof User) {
                $this->logoutUserIfNotConfirmedIdentity($session);
            }

            return $this->redirectToRoute('app_logout');


        }


        //Déconnecte l'utilisateur si trop de requêtes envoyé au serveur.
        if ($session->get('count-call-xhr-request') >= 30) {
            $DOSEvent = new DOSEvent($user);

            $eventDispatcher->dispatch($DOSEvent);
            $session->invalidate();
            return $this->redirectToRoute('app_logout');

        }


        $profilImage = $user->getProfilImage();

        $userInfosRender = $this->arrayInfoUser($user, $user->getIsVerified());



        $userDashboardForm = $this->createForm(UserDashboardType::class, $userInfosRender);

        $userDashboardForm->handleRequest($request);


        if ($userDashboardForm->isSubmitted() && $userDashboardForm->isValid() && $user->getIsVerified()) {

            $formData = [
                'email' => $userDashboardForm->get('email')->getData(),
                'username' => $userDashboardForm->get('username')->getData(),
                'firstname' => $userDashboardForm->get('firstname')->getData(),
                'name' => $userDashboardForm->get('name')->getData(),
                'birth' => $userDashboardForm->get('birth')->getData(),
                'phone' => $userDashboardForm->get('phone')->getData(),
                'gender' => $userDashboardForm->get('gender')->getData(),
            ];

            $profilImageEntered = $userDashboardForm->get('profil_image')->getData();


            if ($profilImageEntered) {

                $profilImage = $this->formateAndSaveImage($profilImageEntered, $slugger);

                $formData += [
                    'profil_image' => $profilImage
                ];


            }

            $userInfos = $userInfosRender += [
                'email' => $user->getUserIdentifier(),
                'username' => $user->getUsername()
            ];

            $this->userUpdate($userInfos, $formData, $user);

            $manager->flush();


        }


        return $this->render('security/userDashboard.html.twig', ['userInfo' => $userInfosRender, 'userDashboardForm' => $userDashboardForm->createView(), 'isVerified' => $user->getIsVerified(), 'profil_image' => $profilImage]);
    }

    /**
     * @param SessionInterface $session
     * @return void
     */
    public function logoutUserIfNotConfirmedIdentity(SessionInterface $session): void
    {
        $session->invalidate();

        $this->addFlash('danger', 'Vous avez été déconnecté car 3 mots de passe invalide entrés');


    }

    /**
     * @param User $user
     * @param bool $isVerified
     * @return array<mixed>
     * Le tableau ser à la fois a la verification des données saisi dans la function userUpdate et
     * dans le placeholder des input du formulaire.
     */
    public function arrayInfoUser(User $user, bool $isVerified): array
    {

        $userInfosRender = [
            'firstname' => $user->getFirstName(),
            'email' => '',
            'username' => '',
            'name' => $user->getName(),
            'birth' => $user->getBirth(),
            'phone' => $user->getPhone(),
            'gender' => $user->getGender(),

        ];
        if ($isVerified) {

            return $userInfosRender;
        }

        foreach ($userInfosRender as &$value) {

            $value = '';

        }

        return $userInfosRender;

    }

    /**
     * @param mixed $profilImage
     * @param SluggerInterface $slugger
     * @return string
     */
    public function formateAndSaveImage(mixed $profilImage, SluggerInterface $slugger): string
    {

        $originalFilename = pathinfo($profilImage->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = strval($this->getParameter('app.profil_image_directory_render')) . $safeFilename . '-' . uniqid() . '.' . $profilImage->guessExtension();
        $profilImage->move($this->getParameter('app.profil_image_directory'), $newFilename);

        return $newFilename;

    }


    /**
     * @param array<mixed> $userInfos
     * @param array<mixed> $formData
     * @param User $user
     * @return void
     * Modifie les information de l'utilisateur si elle sont présente et valide dans le formulaire
     */
    public function userUpdate(array $userInfos, array $formData, User $user): void
    {


        $formData['email'] && $formData['email'] !== $userInfos['email'] ? $user->setEmail($formData['email']) : null;
        $formData['username'] && $formData['username'] !== $userInfos['username'] ? $user->setUsername($formData['username']) : null;
        $formData['firstname'] && $formData['firstname'] !== $userInfos['firstname'] ? $user->setFirstname($formData['firstname']) : null;
        $formData['name'] && $formData['name'] !== $userInfos['name'] ? $user->setName($formData['name']) : null;
        $formData['birth'] && $formData['birth'] !== $userInfos['birth'] ? $user->setBirth($formData['birth']) : null;
        $formData['phone'] && $formData['phone'] !== $userInfos['phone'] ? $user->setPhone($formData['phone']) : null;
        $formData['gender'] && $formData['gender'] !== $userInfos['gender'] ? $user->setGender($formData['gender']) : null;
        array_key_exists('profil_image', $formData) && $formData['profil_image'] !== $user->getProfilImage() ? $user->setProfilImage($formData['profil_image']) : null;


    }


}
