<?php

namespace App\Controller;



use App\Entity\Company;
use App\Entity\User;
use App\Form\addCompanyType;
use App\Service\CheckNumberCompanies;
use App\Service\SaveImages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;


class AddCompanyController extends AbstractController
{


    #[Route('/Ajout-Entreprise', name: 'app_add_company')]
    public function addCompany(Request $request, EntityManagerInterface $manager, SaveImages $saveImages, CheckNumberCompanies $checkerCompanies,): Response
    {

        $user = $this->getUser();

        if (!$user instanceof User) {

            return $this->redirectToRoute('app_login');
        }
        $numberOfCompanies = $checkerCompanies->CheckNumberCompanies($user->getId());


        if ($user->getIsVerified() == 0) {
            $this->addFlash('danger', 'Vous devez d\'abord verifiez votre compte avent de pouvoir ennregistrer une entreprise');

            return $this->redirectToRoute('app_login');
        }

        if ((int)$numberOfCompanies >= 3) {
            $this->addFlash('danger', 'Vous ne pouvez pas enregistrer plus de 3 entreprise');

            return $this->redirectToRoute('app_login');

        }


        $company = new Company();
        $addCompanyForm = $this->createForm(addCompanyType::class, $company);

        $addCompanyForm->handleRequest($request);

        if ($addCompanyForm->isSubmitted() && $addCompanyForm->isValid()) {


            $sectorValue = $this->sectorInputManager($addCompanyForm);

            if (!$sectorValue) {
                return $this->redirectToRoute('app_add_company');
            }


            $profileImage = $saveImages->formateAndSaveImage(
                $addCompanyForm->get('profileImage')->getData(),
                $this->getParameter('app.company_profile_image_directory'),
                $this->getParameter('app.company_profile_image_directory_render'));


            $images = $saveImages->uniteImages($addCompanyForm);


            $company->setProfileImage($profileImage)
                ->setImages($images)
                ->setSector($sectorValue)
                ->setUuid(Uuid::v1());


            $company->setUser($user);
            $user->addCompany($company);
            $manager->persist($company);
            $manager->flush();
            return $this->redirectToRoute('app_user_dashboard');
        }
        return $this->render('addCompany.html.twig', ['addCompanyForm' => $addCompanyForm->createView()]);
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
