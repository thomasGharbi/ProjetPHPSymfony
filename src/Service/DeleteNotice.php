<?php

namespace App\Service;

use App\Entity\Notices;
use Doctrine\ORM\EntityManagerInterface;

class DeleteNotice
{

    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
         $this->entityManager = $entityManager;
    }

    /**
     * @param Notices $notice
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * recalcule les moyennes des avis en supprimant de la moyenne existante l'avis qui doit être supprimé
     */
    public function deleteNotice(Notices $notice){
        if (($notice instanceof Notices)) {

            $company = $notice->getCompany();

            if($company->getCountNotice() == 1){
                $company->setGeneralNotice(0)
                    ->setQualityNotice(0)
                    ->setSpeedNotice(0)
                    ->setPriceNotice(0)
                    ->setCountNotice(-1);
            }else{
                $company->setGeneralNotice(($company->getGeneralNotice() * $company->getCountNotice() - $notice->getGeneralNotice()) / ($company->getCountNotice() - 1) , false)
                    ->setQualityNotice(($company->getQualityNotice() * $company->getCountNotice() - $notice->getQualityNotice()) / ($company->getCountNotice() - 1), false)
                    ->setSpeedNotice(($company->getSpeedNotice() * $company->getCountNotice() - $notice->getSpeedNotice()) / ($company->getCountNotice() - 1), false)
                    ->setPriceNotice(($company->getPriceNotice() * $company->getCountNotice() - $notice->getPriceNotice()) / ($company->getCountNotice() - 1), false)
                    ->setCountNotice(-1);
            }


        }


        $this->entityManager->remove($notice);
        $this->entityManager->flush();
    }

}