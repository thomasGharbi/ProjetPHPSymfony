<?php

namespace App\Controller\Security\Authentication;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;




class ConfirmIdentityController extends AbstractController
{



    #[Route('/confirm-identity', name: 'app_confirm_identity' , defaults: ['public_access' => false],methods: ['GET','POST'])]
    public function confirmIdentity(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        SessionInterface            $session,


    ): JsonResponse|RedirectResponse
    {

        $this->checkIfDosAttack($session);


        //Demande de verification si le mot de passe a déjà était vérifié durant le temps de la session active
        if ($request->headers->get('confirm-password')) {

            $confirm_password = ['password_confirmed' => $session->get('password_confirmed')];
            return $this->json($confirm_password);
        }

        //Demande de Confirmation d'identité avec mot de passe entrée dans le formulaire
        if ($request->headers->get('Confirm-Identity-With-Password')) {

            $data = null;
            $json = $request->getContent();

            if ($json) {
                $data = json_decode(strval($json), true, 512, JSON_THROW_ON_ERROR);
            }


            if (!array_key_exists('password', $data)) {
                throw new HttpException(400, "Le mot de passe doit être saisi.");
            } else {

                $user = $this->getUser();

                if (!$passwordHasher->isPasswordValid($user, $data['password'])) {

                    $this->addFailureAttemptConfirmIdentity($session);

                } else {
                    $session->remove('Password-Confirmation-Invalid');
                    $session->set('password_confirmed', 'valide');
                    $session->set('authorization-modify-password', true);
                }


                $response = ['password_confirmed' => $session->get('password_confirmed'),
                    'attempt_confirm_identity' => $session->get('Password-Confirmation-Invalid'),
                    'login_route' => $this->generateUrl('app_login'),
                    'status_code' => 200];

                return $this->json($response);
            }

        } else {
            return $this->redirectToRoute('app_logout');
        }

    }

    /**
     * @param SessionInterface $session
     * @return void
     * Récupère le nombre de requête XHR effectué durant le temps de la session et la stock
     * le DOSEvent est trigger depuis le UserDashboardController si trop de requête est effectué
     */
    private function checkIfDosAttack(SessionInterface $session): void
    {

        if ($session->get('count-call-xhr-request')) {
            $session->set('count-call-xhr-request', $session->get('count-call-xhr-request') + 1);
        } else {
            $session->set('count-call-xhr-request', 1);
        }

    }

    /**
     * @param SessionInterface $session
     * @return void
     * Récupère le nombre de tentatives de connexions échouées
     */
    private function addFailureAttemptConfirmIdentity(SessionInterface $session): void
    {
        if (!$session->get('Password-Confirmation-Invalid')) {

            $session->set('Password-Confirmation-Invalid', 1);

        } else {

            $session->set('Password-Confirmation-Invalid', $session->get('Password-Confirmation-Invalid') + 1);

        }
    }
}