<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Conversation;
use App\Entity\Messages;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\CompanyRepository;
use App\Repository\ConversationRepository;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use App\Utils\CreateConversationDetails;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Uid\Uuid;

class ConversationController extends AbstractController
{

    private UserRepository         $userRepository;
    private CompanyRepository      $companyRepository;
    private ConversationRepository $conversationRepository;
    private MessagesRepository     $messagesRepository;
    private EntityManagerInterface $entityManager;
    private CreateConversationDetails $conversationDetails;


    public function __construct(
        UserRepository         $userRepository,
        CompanyRepository      $companyRepository,
        ConversationRepository $conversationRepository,
        MessagesRepository     $messagesRepository,
        EntityManagerInterface $entityManager,
        CreateConversationDetails $conversationDetails

    )
    {
        $this->userRepository =         $userRepository;
        $this->companyRepository =      $companyRepository;
        $this->conversationRepository = $conversationRepository;
        $this->messagesRepository =     $messagesRepository;
        $this->entityManager =          $entityManager;
        $this->conversationDetails =    $conversationDetails;

    }

    #[Route('/conversation/{uuidRecipient}/{uuidTalker}', name: 'app_create_conversation')]
    public function createConversation(
        string                 $uuidRecipient,
        string                 $uuidTalker,
        Request                $request,

    ): Response|RedirectResponse
    {


        $user = $this->getUser();

        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }


        $talker = $this->getEntityToUuid($uuidTalker);
        $recipient = $this->getEntityToUuid($uuidRecipient);

        $this->validUuids($talker, $recipient);


        $conversation = $this->conversationRepository->findConversation($recipient, $talker);


//verifie si la conversation existe déjà

        if (!$conversation) {

            $conversation = new Conversation();
            $conversation->setCreatedAt(new DateTimeImmutable())
                ->setUuid(Uuid::v1());
            $this->addEntities($recipient, $conversation);
            $this->addEntities($talker, $conversation);

            $conversationDetails = [
                'who' => $recipient,
                'width' => $talker,
                'conversation' => $conversation];
            $this->entityManager->persist($conversationDetails['conversation']);
        } else {
            $conversationDetails = $this->conversationDetails->createConversationDetails($conversation, $user);

        }


        $messages = $this->messagesRepository->findMessagesByDateTime($conversationDetails);

        if($messages){


        foreach ($messages as $message) {
            if ($message->getCompanyOwner() == $conversationDetails['who']) {

                $message->setIsRead(true);
            } elseif ($message->getUserOwner() == $conversationDetails['who']) {

                $message->setIsRead(true);
            }
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }
        }
        $messageForm = $this->createForm(MessageType::class);
        $messageForm->handleRequest($request);


        //----------------


        if ($messageForm->isSubmitted() && $messageForm->isValid()) {

            $message = new Messages();


            $message->setCreatedAt(new DateTimeImmutable())
                ->setMessage($messageForm->get('message')->getData())
                ->setConversation($conversationDetails['conversation'])
                ->setIsRead();


            $this->addEntities($talker, $message);


            $this->entityManager->persist($message);
            $this->entityManager->flush();


            return $this->redirectToRoute('app_conversation', ['uuidConversation' => $conversationDetails['conversation']->getUuid()]);


        }

        return $this->render('conversations/conversation.html.twig', [
            'controller_name' => 'ConversationController', 'messages' => $messages, 'conversation' => $conversationDetails, 'messageForm' => $messageForm->createView()
        ]);
    }







    #[Route('/conversation/{uuidConversation}', name: 'app_conversation')]
    public function conversations(
        string                 $uuidConversation,
        Request                $request,
    ):Response|RedirectResponse
    {

        $conversation = $this->conversationRepository->findOneBy(['uuid' => $uuidConversation]);

        $user = $this->getUser();
        if (!$conversation ||  !($user instanceof User)) {
            throw new AccessDeniedException();
        }


        $conversationDetails = $this->conversationDetails->createConversationDetails($conversation, $user);

        //récupère les messages triés par date
        $messages = $this->messagesRepository->findMessagesByDateTime($conversationDetails);

        if($messages) {

            foreach ($messages as $message) {

                if ($message->getCompanyOwner() == $conversationDetails['who']  ) {

                    $message->setIsRead(true);
                } elseif ($message->getUserOwner() == $conversationDetails['who'] ) {

                    //dd('ok2');
                    $message->setIsRead(true);
                }elseif ($message->getUserOwner() == null && $message->getCompanyOwner() == null){

                    $message->setIsRead(true);
                }
            }
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }
        $messageForm = $this->createForm(MessageType::class);
        $messageForm->handleRequest($request);


        if ($messageForm->isSubmitted() && $messageForm->isValid()) {

            $message = new Messages();


            $message->setCreatedAt(new DateTimeImmutable())
                ->setMessage($messageForm->get('message')->getData())
                ->setConversation($conversationDetails['conversation'])
                ->setIsRead();


            $this->addEntities($conversationDetails['width'], $message);


            $this->entityManager->persist($message);
            $this->entityManager->flush();


            return $this->redirectToRoute('app_conversation', ['uuidConversation' => $conversationDetails['conversation']->getUuid()]);


        }
        //dd($messages,$conversationDetails);

        return $this->render('conversations/conversation.html.twig', [
            'controller_name' => 'ConversationController', 'messages' => $messages, 'conversation' => $conversationDetails, 'messageForm' => $messageForm->createView()
        ]);

    }





    /**
     * @param string $uuid
     * @return User|Company
     * Récupère les entités associés aux Uuids
     */
    public function getEntityToUuid(string $uuid): User|Company
    {

        $entity = $this->userRepository->findOneBy(['uuid' => $uuid]);

        if (is_null($entity)) {
            $entity = $this->companyRepository->findOneBy(['uuid' => $uuid]);
        }
        if (is_null($entity)) {

            throw new NotFoundHttpException();
        }

        return $entity;
    }

    /**
     * @param User|Company $uuid1
     * @param User|Company $uuid2
     * @return void
     */
    public function validUuids(User|Company $uuid1,User|Company $uuid2): void
    {

        if ($uuid1 === $uuid2) {

            throw new NotFoundHttpException();
        }
        if ($uuid1 instanceof User) {
            if ($uuid2 instanceof Company && $uuid1->getCompanies()->contains($uuid2)) {

                throw new NotFoundHttpException();
            }
        } elseif ($uuid1 instanceof Company) {
            $user = $uuid1->getUser();
            if ($uuid2 instanceof User && $uuid2->getCompanies()->contains($uuid1)) {

                throw new NotFoundHttpException();
            } elseif ($user->getCompanies()->contains($uuid1) && $user->getCompanies()->contains($uuid2)) {

                throw new NotFoundHttpException();
            }
        }
    }


    /**
     * @param User|Company $entity
     * @param Conversation|Messages $conversationOrMessage
     * @return void
     */
    public function addEntities(User|Company $entity,Conversation|Messages $conversationOrMessage): void
    {
        if ($conversationOrMessage instanceof Conversation) {
            if ($entity instanceof Company) {

                $conversationOrMessage->addCompany($entity);

            } elseif ($entity instanceof User) {
                $conversationOrMessage->addUser($entity);
            }
        } elseif ($conversationOrMessage instanceof Messages) {
            if ($entity instanceof Company) {

                $conversationOrMessage->setCompanyOwner($entity);

            } elseif ($entity instanceof User) {
                $conversationOrMessage->setUserOwner($entity);
            }
        }


    }


}