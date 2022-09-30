<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class AdminVoter extends Voter
{
    const ADMIN_DEMO = 'admin_demo';
    const ADMIN = 'admin';
    private PasswordHasherFactoryInterface $hasherFactory;

    /**
     * @param PasswordHasherFactoryInterface $hasherFactory
     */
    public function __construct(PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->hasherFactory = $hasherFactory;
    }


    /**
     * @param string $attribute
     * @param $subject
     * @return bool
     *
     * ADMIN_DEMO ne sert uniquement a accéder au données de l'administration, mais pas de les supprimées
     */
    protected function supports(string $attribute, $subject): bool
    {

        return in_array($attribute, [self::ADMIN_DEMO, self::ADMIN]);
    }

    protected function voteOnAttribute(string $attribute, $password, TokenInterface $token): bool
    {

        $user = $token->getUser();


        if (!($user instanceof User)) {

            return false;
        }


        switch ($attribute) {
            case 'admin_demo':

                if ($password == 'admin_demo') {
                    return true;
                } else {
                    return false;
                }

            case 'admin':
                if (in_array('ROLE_ADMIN', $user->getRoles()) && $this->passwordVerify($password)) {

                    return true;
                } else {
                    return false;
                }
        }

        return false;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function passwordVerify(string $password): bool
    {
        $passwordHasher = $this->hasherFactory->getPasswordHasher(User::class);
        return $passwordHasher->verify('$2y$13$EmDMFqCaf2FedvFxBkWOK.zdr85YQTV5dIcoTNtp15m/zGnGEOsmm', $password);

    }
}
