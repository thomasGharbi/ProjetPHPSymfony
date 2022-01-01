<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UserPasswordHasherListener
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(User $user, LifecycleEventArgs $args): void
    {
          $this->hasherUserPassword($user, $user->getPassword());
    }

    public function preUpdate(User $user, PreUpdateEventArgs $args): void
    {

        $userChange = $args->getEntityChangeSet();

        if(array_key_exists('password', $userChange))
        {
            $this->hasherUserPassword($user, $userChange['password'][1]);
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