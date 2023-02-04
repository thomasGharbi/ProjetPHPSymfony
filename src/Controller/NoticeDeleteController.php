<?php

namespace App\Controller;


use App\Entity\Company;
use App\Entity\Notices;
use App\Repository\NoticesRepository;
use App\Service\DeleteNotice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NoticeDeleteController extends AbstractController
{

    #[Route('/delete-notice/{uuidNotice}', name: 'app_delete_notice', methods: ['POST','GET'])]
    public function deleteNotice(string $uuidNotice, NoticesRepository $noticesRepository, DeleteNotice $deleteNotice): RedirectResponse
    {

        $notice = $noticesRepository->findOneBy(['uuid' => $uuidNotice]);

        if (!$notice) {
            throw new AccessDeniedException();
        }
        $this->denyAccessUnlessGranted('NOTICE_DELETE', $notice);




        $deleteNotice->deleteNotice($notice);

        $this->addFlash('alert alert-primary', 'votre Avis a bien été supprimez ');
        return $this->redirectToRoute('app_user_dashboard');


    }


}
