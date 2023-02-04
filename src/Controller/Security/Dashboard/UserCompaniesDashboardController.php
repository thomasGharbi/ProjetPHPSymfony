<?php

namespace App\Controller\Security\Dashboard;

use App\Entity\Company;
use App\Entity\User;
use App\Form\Security\Dashboard\DeleteCompanyType;
use App\Form\Security\Dashboard\UserCompaniesDashboardType;
use App\Repository\CompanyRepository;
use App\Service\ConfirmIdentitySecurity;
use App\Service\DeleteCompany;
use App\Service\SaveImages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserCompaniesDashboardController extends AbstractController
{
    #[Route('/espace-entreprise/{uuidCompany}', name: 'app_company_dashboard', defaults: ['public_access' => false], methods: ['GET','POST'])]
    public function companyDashboard(
        string                  $uuidCompany,
        Request                 $request,
        EntityManagerInterface  $entityManager,
        CompanyRepository       $companyRepository,
        SaveImages              $saveImages,
        ConfirmIdentitySecurity $confirmIdentitySecurity,
        DeleteCompany           $deleteCompany,

    ): Response
    {

        /**
         * @var null|User $user
         */
        $user = $this->getUser();

        //verifie si l'utilisateur est present et a confirmé son identité
        $user = $confirmIdentitySecurity->checkConfirmIdentityRequest($user);

        $company = $companyRepository->findOneBy(['uuid' => $uuidCompany]);


        if (!$company || !($user instanceof User) || !$user->getCompanies()->contains($company)) {

            return $this->redirectToRoute('app_logout');
        }


        $deleteCompanyForm = $this->createForm(DeleteCompanyType::class);
        $deleteCompanyForm->handleRequest($request);

        if ($deleteCompanyForm->isSubmitted() && $deleteCompanyForm->isValid()) {

            $confirmIdentitySecurity->checkIfBypassConfirmIdentity();


            $deleteCompany->deleteCompany($company);
            // $entityManager->remove($company);
            // $entityManager->flush();
            $this->addFlash('alert alert-primary', 'votre entreprise a été supprimez ');
            return $this->redirectToRoute('app_user_dashboard');
        }


        $companyDashboardForm = $this->createForm(UserCompaniesDashboardType::class, $company);
        $companyDashboardForm->handleRequest($request);



        if ($companyDashboardForm->isSubmitted() && $companyDashboardForm->isValid()) {

            //verifie si l'utisateur a modifier le HTML pour bypass la confirmation d'dentité
            $confirmIdentitySecurity->checkIfBypassConfirmIdentity();


            $this->setInCompany($companyDashboardForm, $company, $saveImages);

            $entityManager->flush();

            return $this->redirectToRoute($request->get('_route'), ['uuidCompany' => $uuidCompany]);

        }

        return $this->render('security/dashboard/companyDashboard.html.twig', ['companyDashboardForm' => $companyDashboardForm->createView(), 'companyDelete' => $deleteCompanyForm->createView(), 'company' => $company]);
    }



    /**
     * @param FormInterface<mixed> $companyDashboardForm
     * @param Company $company
     * @param SaveImages $saveImages
     * @return void
     */
    public function setInCompany(FormInterface $companyDashboardForm, Company $company, SaveImages $saveImages): void
    {


        $sector = $this->sectorInputManager($companyDashboardForm);
        if (!$sector) {
            throw new LogicException('le formulaire saisi est invalide');
        } else {
            $company->setSector($sector);
        }


        if ($companyDashboardForm->get('SIRETNumber')->getData()) {
            $company->setSIRETNumber($companyDashboardForm->get('SIRETNumber')->getData());
        }

        $images = [

            $companyDashboardForm->get('image1')->getData(),
            $companyDashboardForm->get('image2')->getData(),
            $companyDashboardForm->get('image3')->getData(),
            $companyDashboardForm->get('image4')->getData(),
            $companyDashboardForm->get('image5')->getData()
        ];
        $deleteImages = [
            null,
            $companyDashboardForm->get('deleteImage2')->getData(),
            $companyDashboardForm->get('deleteImage3')->getData(),
            $companyDashboardForm->get('deleteImage4')->getData(),
            $companyDashboardForm->get('deleteImage5')->getData(),
        ];


        $profileImage = $companyDashboardForm->get('profileImage')->getData();

        $this->sortingImages($deleteImages, $images, $saveImages, $company, $profileImage);


    }

    /**
     * @param array<mixed> $deleteImages
     * @param array<mixed> $formImages
     * @param SaveImages $saveImages
     * @param Company $company
     * @param mixed $profileImage
     * @return void
     * Gestion des images en fonction des données du formulaire.
     */
    public function sortingImages(array $deleteImages, array $formImages, SaveImages $saveImages, Company $company, mixed $profileImage): void
    {

        if ($profileImage && $profileImage->getClientOriginalName() !== $company->getProfileImage()) {
            $profileImageFormated = $saveImages->formateAndSaveImage(
                $profileImage,
                $this->getParameter('app.company_profile_image_directory'),
                $this->getParameter('app.company_profile_image_directory_render'));
            $company->setProfileImage($profileImageFormated);
        }
        /**
         * @var array<string|null> $imagesArray
         */
        $imagesArray = [];
        foreach ($formImages as $key => $formImage) {

            if (!$formImage) {

                if ($deleteImages[$key]) {

                    $imagesArray[] = null;
                } else {

                    $imagesArray[] = $company->getImages()[$key] ?? null;
                }

            } else {

                $imagesArray[] = $saveImages->formateAndSaveImage(
                    $formImages[$key],
                    $this->getParameter('app.company_image_directory'),
                    $this->getParameter('app.company_image_directory_render'));


            }


        }


        $company->setImages(array_filter($imagesArray));


    }

    /**
     * @param FormInterface<mixed> $form
     * @return string|null
     * Gere le secteur d'activité si il ne correspond pas au secteurs proposés
     */
    public function sectorInputManager(FormInterface $form): ?string
    {

        if ($form->get('sector')->getData() == 'autre' && $form->get('otherSector')->getData()) {

            return $form->get('otherSector')->getData();
        } elseif ($form->get('sector')->getData() != 'autre') {
            return $form->get('sector')->getData();
        }
        return null;
    }
}








