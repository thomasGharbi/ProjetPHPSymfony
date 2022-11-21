<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\MessagesRepository;
use App\Repository\VisitorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class DeleteUser
{

    private MessagesRepository $messagesRepository;
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $parameterBag;
    private DeleteCompany         $deleteCompany;
    private VisitorRepository     $visitorRepository;


    public function __construct(
        MessagesRepository     $messagesRepository,
        EntityManagerInterface $entityManager,
        ParameterBagInterface  $parameterBag,
        DeleteCompany          $deleteCompany,
        VisitorRepository      $visitorRepository)
    {
        $this->messagesRepository = $messagesRepository;
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
        $this->deleteCompany = $deleteCompany;
        $this->visitorRepository = $visitorRepository;
    }


    /**
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user):void
    {

        $messages = $this->messagesRepository->findBy(['userOwner' => $user]);


        $profileImage = $this->userFilesManagement($user);
        $arrayTalkerDelete =  $this->deleteUserInConversation($user, $profileImage);
        $this->deleteCompanyOfUser($user);
        $this->visitorRepository->deleteUserOfVisit($user);


        foreach ($messages as $message){
            $message->setUserOwner(null);
            $message->setTalkerDeleted($arrayTalkerDelete);
        }


        $this->entityManager->remove($user);
        $this->entityManager->flush();

    }


    /**
     * @param User $user
     * @return mixed
     * déplace dans un dossier l'image de profile pour les conversations créées
     */
    public function userFilesManagement(User $user): mixed
    {

        if($user->getProfileImage() == '/uploads/profil_image_default/user_profil_image_default.jpg'){
            return $user->getProfileImage();
        }


        $filesystem = new Filesystem();

        $profileImage = str_replace('/', '\\' ,$this->parameterBag->get('app.profile_image_directory') . '\\' . substr((string)$user->getProfileImage(),30));
        $newFile = str_replace('/', '\\' ,$this->parameterBag->get('app.entities_profile_image_directory_delete') . '\\' . substr((string)$user->getProfileImage(),30));
        $filesystem->rename($profileImage, $newFile);


        return $newFile;

    }

    /**
     * @param User $user
     * @param mixed $profileImage
     * @return array<mixed>
     */
    public function deleteUserInConversation(User $user, mixed $profileImage)
    {
        //infos pour garder une traces de l'utilisateur supprimées dans les conversations créées
        $arrayTalkerDelete = ['entity' => 'user', 'profileImage' => $profileImage, 'nameOfEntity' => $user->getUsername(), 'uuid' => $user->getUuid()];
        $conversations = $user->getConversations();
        foreach ($conversations as $conversation) {

            foreach ($conversation->getUsers() as $companyInConversation) {

                if ($user == $companyInConversation) {

                    $conversation->removeCompany($companyInConversation);
                    $conversation->setTalkerDeleted($arrayTalkerDelete);



                }

            }
        }
        return $arrayTalkerDelete;
    }

    /**
     * @param User $user
     * @return void
     */
    public function deleteCompanyOfUser(User $user):void
    {
        foreach ($user->getCompanies() as $company){

            $this->deleteCompany->deleteCompany($company);

        }
    }



}