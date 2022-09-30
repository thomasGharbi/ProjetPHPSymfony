<?php

namespace App\Security;

use App\Entity\AuthenticationLog;
use App\Entity\User;
use App\Repository\AuthenticationLogRepository;
use Doctrine\ORM\EntityManagerInterface;


class BrutForceChecker
{
    public EntityManagerInterface $entityManager;
    public AuthenticationLogRepository $authRepository;


    public function __construct(
        EntityManagerInterface $entityManager,
        AuthenticationLogRepository $authRepository,


    )
    {
        $this->entityManager = $entityManager;
        $this->authRepository = $authRepository;


    }

    /**
     * @param string $userIp
     * @param string $emailEntered
     * @return void
     */
    public function addAuthenticationFailure(string $userIp,?string $emailEntered, bool $oauth = false, ?string $oauthProvider = null): void
    {
        $this->authRepository->addAuthenticationFailure($userIp, $emailEntered, $oauth, $oauthProvider);

    }

    /**
     * @param User $user
     * @param null|string $userIp
     * @return bool
     */
    public function addAdminAttemptFailure(User $user,?string $userIp): bool{


        if($userIp == null ){
            $userIp = ' pas d\'adresse ip enregistré';
        }
         $isBlacklisted = $this->authRepository->addAuthenticationFailure($userIp, $user->getEmail(), false, null, true);

         if($isBlacklisted instanceof \DateTime){
             return true;
         }

         return false;


    }

    /**
     * @param string $userIp
     * @return null|string
     * Verifie l'adresse ip de l'utilisateur courant et si son adresse ip
     * est black listé, renvoie l'heure de la fin du black liste
     */
    public function checkIfBlackListed(string $userIp): ?string
    {
        $isBlackListed = $this->authRepository->getIpBlackListed($userIp);

        if($isBlackListed instanceof AuthenticationLog){

            return $isBlackListed->getBlackListedUntil()?->format('H:i');

        }
        return null;
    }


}
