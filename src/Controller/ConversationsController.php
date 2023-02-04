<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Utils\CreateConversationDetails;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ConversationsController extends AbstractController
{
    #[Route('/Conversations', name: 'app_conversations', defaults: ['public_access' => false], methods: ['GET','POST'])]
    public function conversations(
        ConversationRepository $conversationRepository,
        CreateConversationDetails $conversationDetails): Response
    {
        /**
         * @var null|User $user
         */
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException();
        }

        if (($user instanceof User)  ) {
            $allEntities = [$user];
            foreach ($user->getCompanies() as $company) {
                $allEntities[] = $company;
            }
        }

        $brutConversations = $conversationRepository->findConversationByDateTime($allEntities);

        $conversations = null;
        foreach ($brutConversations as $conversation) {
            $conversations[] = $conversationDetails->createConversationDetails($conversation, $user);
        }


        return $this->render('conversations/conversations.html.twig', [
            'controller_name' => 'ConversationsController', 'conversations' => $conversations,
            'entities' => $allEntities
        ]);
    }



}
