<?php

namespace App\Service;

use _PHPStan_76800bfb5\Symfony\Component\Console\Exception\LogicException;
use App\Entity\User;
use App\Event\DOSEvent;
use http\Exception\InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class ConfirmIdentitySecurity
{
    private RequestStack $requestStack;
    private SessionInterface $session;
    private EventDispatcherInterface $eventDispatcher;
    private RouterInterface $router;

    public function __construct(

        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router,
        RequestStack $requestStack
    )
    {
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->router = $router;
        $this->session = $this->requestStack->getSession();


    }

    /**
     * @param User|null $user
     * @return User|null
     */
    public function checkConfirmIdentityRequest(?User $user): ?User
    {


        if ($this->session == null || $this->session->get('Password-Confirmation-Invalid') >= 3 || !($user instanceof User)) {

            if ($user instanceof User) {
                $this->logoutUserIfNotConfirmedIdentity();

            }

            return null;
        }elseif($this->session->get('count-call-xhr-request') >= 30) {
            $DOSEvent = new DOSEvent($user);

            $this->eventDispatcher->dispatch($DOSEvent);
            $this->session->invalidate();
            return null;

        }

        return $user;
        //Déconnecte l'utilisateur si trop de requêtes envoyé au serveur.
    }


    /**
     * @return void
     */
    public function checkIfBypassConfirmIdentity(): void
    {
        //verifie si il ya eu bypass de verification d'identité (via modification de l'HTML)

        if($this->session->get('password_confirmed') !== 'valide'){

            throw new LogicException('Le formulaire rempli est invalide');
        }

    }


    /**
     * @return void
     */
    public function logoutUserIfNotConfirmedIdentity(): void
    {
        $this->session->invalidate();

        //erreur PHPStan methode getFlashBag()
        if ($this->session instanceof Session){
            $this->session->getFlashBag()->add('danger', 'Vous avez été déconnecté car 3 mots de passe invalide entrés');
        }


    }

}