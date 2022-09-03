<?php

namespace App\Utils;


use App\Entity\Conversation;
use App\Entity\User;

class CreateConversationDetails {

    /**
     * @param Conversation $conversation
     * @param User $user
     * @return array<mixed>
     * crée un tableau avec les informations de la conversation plus simple a implémenter
     */
    public function createConversationDetails(Conversation $conversation,User $user): array
    {
        $who = $width = $isRead = null;
        if (!$conversation->getUsers()->isEmpty()) {

            foreach ($conversation->getUsers() as $userConversation) {

                if ($userConversation != $user) {

                    $who = $userConversation;
                } else {

                    $width = $userConversation;
                }
            }

        }

        if (!$conversation->getCompanies()->isEmpty()) {

            foreach ($conversation->getCompanies() as $companyConversation) {

                if (!$user->getCompanies()->contains($companyConversation)) {

                    $who = $companyConversation;
                } else {

                    $width = $companyConversation;
                }
            }

        }

        foreach ($conversation->getMessages() as $message) {

            if (($message->getCompanyOwner() == $who || $message->getUserOwner() == $who) && $message->getIsRead() == false) {

                $isRead = false;
            } else {
                $isRead = true;
            }
        }

        return ['who' => $who,
            'width' => $width,
            'conversation' => $conversation,
            'is_read' => $isRead];
    }

}