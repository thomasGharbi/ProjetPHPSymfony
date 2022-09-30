<?php

namespace App\Service;


use App\Entity\Company;
use App\Repository\MessagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;



class DeleteCompany{

    private MessagesRepository $messagesRepository;
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $parameterBag;

    /**
     * @param MessagesRepository $messagesRepository
     */
    public function __construct(
        MessagesRepository $messagesRepository,
        EntityManagerInterface $entityManager,
        ParameterBagInterface $parameterBag)
    {
        $this->messagesRepository = $messagesRepository;
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
    }


    /**
     * @param Company $company
     * @return void
     */
    public function deleteCompany(Company $company):void
    {

        $messages = $this->messagesRepository->findBy(['companyOwner' => $company]);


        $profileImage = $this->companyFilesManagement($company);
        $arrayTalkerDeleted = $this->deleteCompanyInConversation($company, $profileImage);

        foreach ($messages as $message){
            $message->setCompanyOwner(null);
            $message->setTalkerDeleted($arrayTalkerDeleted);
        }


        $this->entityManager->remove($company);
        $this->entityManager->flush();





    }

    /**
     * @param Company $company
     * @param mixed $profileImage
     * @return array<mixed>
     */
    public function deleteCompanyInConversation(Company $company,mixed $profileImage):array
    {
        //infos pour garder une trace de l'entreprise supprimées dans les conversations créées

        $arrayTalkerDeleted = ['entity' => 'company', 'profileImage' => $profileImage, 'nameOfEntity' => $company->getNameOfCompany(), 'uuid' => $company->getUuid()];
        $conversations = $company->getConversations();

        foreach ($conversations as $conversation) {

            foreach ($conversation->getCompanies() as $companyInConversation) {

                if ($company == $companyInConversation) {

                    $conversation->removeCompany($company);
                    $conversation->setTalkerDeleted($arrayTalkerDeleted);



                }

            }
        }


        return $arrayTalkerDeleted;
    }

    /**
     * @param Company $company
     * @return mixed
     *
     * supprime les images de l'entreprise et déplace dans un dossier l'image de profile pour les conversations créées
     */
    public function companyFilesManagement(Company $company):mixed
    {

        $filesystem = new Filesystem();



        $profileImage = str_replace('/', '\\' ,$this->parameterBag->get('app.company_profile_image_directory') . '\\' . substr($company->getProfileImage(),30));
         $newFile = str_replace('/', '\\' ,$this->parameterBag->get('app.entities_profile_image_directory_delete') . '\\' . substr($company->getProfileImage(),30));
         $newFileRender = str_replace('/', '\\' ,$this->parameterBag->get('app.entities_profile_image_directory_delete_render') . '\\' . substr($company->getProfileImage(),30));
         $filesystem->rename($profileImage, $newFile);


        foreach ($company->getImages() as $image){
            $filesystem->remove(str_replace('/', '\\' ,$this->parameterBag->get('app.company_image_directory') . '\\' . substr($image,22)));
        }



        return $newFileRender  ;

    }



}