<?php

namespace App\Controller;


use App\Entity\Company;
use App\Entity\Notices;
use App\Repository\NoticesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NoticeDeleteController extends AbstractController
{

    #[Route('/delete-notice/{uuidNotice}', name: 'app_delete_notice', methods: ['POST'])]
    public function deleteNotice(string $uuidNotice, NoticesRepository $noticesRepository, EntityManagerInterface $entityManager): RedirectResponse
    {

        $notice = $noticesRepository->findOneBy(['uuid' => $uuidNotice]);

        if (!$notice) {
            throw new AccessDeniedException();
        }
        $this->denyAccessUnlessGranted('NOTICE_DELETE', $notice);




        //recalcule les moyennes des avis en supprimant de la moyenne existante l'avis qui doit être supprimé
        if (($notice instanceof Notices)) {

            $company = $notice->getCompany();

            $company->setGeneralNotice(($company->getGeneralNotice() * $company->getCountNotice() - $notice->getGeneralNotice()) / ($company->getCountNotice() - 1), false)
                ->setQualityNotice(($company->getQualityNotice() * $company->getCountNotice() - $notice->getQualityNotice()) / ($company->getCountNotice() - 1), false)
                ->setSpeedNotice(($company->getSpeedNotice() * $company->getCountNotice() - $notice->getSpeedNotice()) / ($company->getCountNotice() - 1), false)
                ->setPriceNotice(($company->getPriceNotice() * $company->getCountNotice() - $notice->getPriceNotice()) / ($company->getCountNotice() - 1), false)
                ->setCountNotice(-1);
        }


        $entityManager->remove($notice);
        $entityManager->flush();
        $this->addFlash('alert alert-primary', 'votre Avis a bien été supprimez ');
        return $this->redirectToRoute('app_user_dashboard');


    }


}
