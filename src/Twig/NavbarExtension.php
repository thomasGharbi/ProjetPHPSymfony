<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NavbarExtension extends AbstractExtension
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('navbar', [$this, 'navbarData']),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function navbarData(): array
    {
        $navbarData = [];
        $user = $this->security->getUser();


        if ($user instanceof User) {
            $navbarData['user'] = $user;
            if (!empty($user->getCompanies())) {
                foreach ($user->getCompanies() as $company) {

                    $navbarData['companies'][] = $company;

                }
            }

            $navbarData['conversation_is_read'] = $this->conversationsAreUnread($user);


        }


        return $navbarData;
    }

    /**
     * @param User $user
     * @return bool
     *
     * vérifie si il y a des conversations non-lues
     */
    public function conversationsAreUnread(User $user): bool
    {


        $allEntities = [$user];
        foreach ($user->getCompanies() as $company) {
            $allEntities[] = $company;
        }

        foreach ($allEntities as $entity) {
            $conversations = $entity->getConversations();
            foreach ($conversations as $conversation) {
                foreach ($conversation->getMessages() as $message)
                    if ($message->getIsRead() == false && $message->getUserOwner() !== $entity && $message->getCompanyOwner() !== $entity && empty($message->getTalkerDeleted())) {
                        return true;
                    }
            }


        }
        return false;
    }
}
