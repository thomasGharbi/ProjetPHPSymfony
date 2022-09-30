<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Notices;
use App\Entity\User;
use App\Form\CreateConversationType;
use App\Form\CreateNoticeType;
use App\Repository\CompanyRepository;
use App\Service\AddVisitor;
use App\Service\SaveImages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CompanyController extends AbstractController
{
    #[Route('/entreprise/{uuidCompany}', name: 'app_company_page')]
    public function index(
        mixed                  $uuidCompany,
        CompanyRepository      $companyRepository,
        CreateConversationType $conversationType,
        Request                $request,
        EntityManagerInterface $entityManager,
        SaveImages             $saveImages,
        AddVisitor             $addVisitor): Response|RedirectResponse
    {

        $company = $companyRepository->findOneBy(['uuid' => $uuidCompany]);
        if (!$company) {
            throw new HttpException(404);
        }


        /**
        * @var null|User $user
        */
        $user = $this->getUser();

        $notices = $this->noticesArrayForView($company, $user);
        $formView = $createNoticeForm = null;


        $addVisitor->addPointToVisitor('visitor_company', 2);
        //récupère dans un tableau, toutes les entreprises et l'utilisateur courant dans un tableau
        if (($user instanceof User) && $user !== $company->getUser() ) {
            $allEntities = [$user];
            foreach ($user->getCompanies() as $companyInArray) {
                $allEntities[] = $companyInArray;
            }


            $createConversationForm = $this->createForm($conversationType::class, $allEntities);
            $createConversationForm->handleRequest($request);

            if ($createConversationForm->isSubmitted() && $createConversationForm->isValid()) {
                $talker = $createConversationForm->get('talker')->getData();
                return $this->redirectToRoute('app_create_conversation', ['uuidRecipient' => $company->getUuid(), 'uuidTalker' => $talker]);
            }

            $notice = new Notices;

            $createNoticeForm = $this->createForm(CreateNoticeType::class, $notice);
            $createNoticeForm->handleRequest($request);
            $formView = $createConversationForm->createView();

            if ($createNoticeForm->isSubmitted() && $createNoticeForm->isValid()) {
                $images = $saveImages->uniteImages($createNoticeForm);

                $notice->setUser($user)
                    ->setCompany($company)
                    ->setImages($images);

                $company->setCountNotice(1)
                    ->setGeneralNotice($createNoticeForm->get('generalNotice')->getData())
                    ->setQualityNotice($createNoticeForm->get('qualityNotice')->getData())
                    ->setSpeedNotice($createNoticeForm->get('speedNotice')->getData())
                    ->setPriceNotice($createNoticeForm->get('priceNotice')->getData());

                $entityManager->persist($notice);
                $entityManager->flush();
                return $this->redirectToRoute('app_company_page', ['uuidCompany' => $uuidCompany]);
            }


        }


        return $this->render('company.html.twig', [
            'company' => $company, 'conversationForm' => $formView, 'noticeForm' => $createNoticeForm?->createView(), 'notices' => $notices
        ]);
    }


    /**
     * @param Company $company
     * @param UserInterface|null $user
     * @return array<mixed>|null
     * sépare dans un tableau les avis qui appartiennent à  l'utilisateur connecté
     */
    public function noticesArrayForView(Company $company, UserInterface|null $user): ?array
    {
        $noticesArray = null;

        foreach ($company->getNotices() as $notice) {

            if (!is_null($user) && $user == $notice->getUser()) {
                $noticesArray['owner_notices'][] = $notice;
            } else {
                $noticesArray['notices'][] = $notice;
            }
        }

        return $noticesArray;
    }


}
