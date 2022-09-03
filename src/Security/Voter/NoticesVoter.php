<?php

namespace App\Security\Voter;

use App\Entity\Notices;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class NoticesVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {


        return in_array($attribute, ['NOTICE_DELETE'])
            && $subject instanceof Notices;
    }

    protected function voteOnAttribute(string $attribute, $notice, TokenInterface $token): bool
    {

        $user = $token->getUser();


        if (!($user instanceof User)) {

            return false;
        }

        if (null == $notice->getUser()) {

            return false;
        }
        switch ($attribute) {
            case 'NOTICE_DELETE':
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                } elseif ($notice->getUser()->getId() == $user->getId()) {
                    return true;
                }
                break;
        }
        return false;
    }
}
