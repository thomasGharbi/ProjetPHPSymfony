<?php

namespace App\Utils;


use App\Entity\Company;
use App\Entity\Conversation;
use App\Entity\User;

class CreateConversationDetails
{

    /**
     * @param Conversation $conversation
     * @param User $user
     * @return array<mixed>
     * crée un tableau avec les informations de la conversation plus simple a implémenter
     */
    public function createConversationDetails(Conversation $conversation, User $user): array
    {

        $who = $width =  null;

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

        //dans le cas où il y aurait une entité supprimer par l'utilisateur
        if ($who == null && $conversation->getTalkerDeleted() !== null) {
            $who = $conversation->getTalkerDeleted();
        } elseif ($width == null && $conversation->getTalkerDeleted() !== null) {
            $width = $conversation->getTalkerDeleted();
        }


        $isRead = $this->isRead($conversation, $who);


        return ['who' => $who,
            'width' => $width,
            'conversation' => $conversation,
            'is_read' => $isRead];

    }


    /**
     * @param Conversation $conversation
     * @param mixed $who
     * @return bool
     * verifie si tous les messages (destiné à l'utilisateur courant ) ont étaient ouvert
     */
    public function isRead(Conversation $conversation, mixed $who): bool
    {
        $isRead = false;
        foreach ($conversation->getMessages() as $message) {

            // modifie les messages qui on était émi par l'interlocuteur (ou un interlocuteur ayant était supprimé) comme lu
            if (($message->getCompanyOwner() == $who || $message->getUserOwner() == $who) && $message->getIsRead() == false) {


                $isRead = false;
                break;

            } else {
                $isRead = true;

            }

            if (!empty($message->getTalkerDeleted())) {

                $isRead = true;

            }
        }
        return $isRead;
    }

}