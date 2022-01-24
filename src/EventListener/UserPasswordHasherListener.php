<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UserPasswordHasherListener
{
    private UserPasswordHasherInterface $hasher;
    private LoggerInterface $securityLogger;

    public function __construct(UserPasswordHasherInterface $hasher, LoggerInterface $securityLogger)
    {
        $this->hasher = $hasher;
        $this->securityLogger = $securityLogger;
    }

    public function prePersist(User $user, LifecycleEventArgs $args): void
    {
          $this->hasherUserPassword($user, $user->getPassword());
    }

    public function preUpdate(User $user, PreUpdateEventArgs $args): void
    {

        $userChange = $args->getEntityChangeSet();
        $userEmail = $user->getEmail();
        if(array_key_exists('password', $userChange))
        {
            $this->hasherUserPassword($user, $userChange['password'][1]);
            $this->securityLogger->info("'AUTHENTICATION (MODIFY PASSWORD)': l'utilisateur avec l'adresse email '$userEmail' a effectué une modification de mot de passe ");
        }
    }



    /**
     * @param User $user
     * @param string $password
     * @return void
     */
    private function hasherUserPassword(User $user, string $password): void
    {

        $user->setPassword($this->hasher->hashPassword($user, $password));


    }
}