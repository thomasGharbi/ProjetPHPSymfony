<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Form\CreateConversationType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    #[Route('/utilisateur/{uuidUser}', name: 'app_user_page')]
    public function userPage(string $uuidUser, UserRepository $userRepository, CreateConversationType $conversationType, Request $request): Response
    {

        $userConcerned = $userRepository->findOneBy(['uuid' => $uuidUser]);
        if (!$userConcerned) {
            throw new HttpException(404);
        }

       $formView = null;
        $userHimSelf = false;
        $user = $this->getUser();
        $allEntities = [$user];

        if(($user instanceof User) && !empty($user->getCompanies()) && $user !== $userConcerned) {
            foreach ($user->getCompanies() as $company) {
                $allEntities[] = $company;
            }

//dd($allEntities);
            $createConversationForm = $this->createForm($conversationType::class, $allEntities);
            $createConversationForm->handleRequest($request);
            if ($createConversationForm->isSubmitted() && $createConversationForm->isValid()) {
                $talker = $createConversationForm->get('talker')->getData();
                return $this->redirectToRoute('app_create_conversation', ['uuidRecipient' => $userConcerned->getUuid(), 'uuidTalker' => $talker]);
            }
            $formView = $createConversationForm->createView();

        }
        if ($user === $userConcerned) {

            $userHimSelf = true;
        }


        return $this->render('user.html.twig', [
            'user' => $userConcerned, 'conversationForm' => $formView, 'notices' => $userConcerned->getNotices(), 'him_self' => $userHimSelf,
        ]);
    }


}
