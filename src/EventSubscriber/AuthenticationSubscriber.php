<?php


namespace App\EventSubscriber;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AuthenticationSubscriber implements EventSubscriberInterface
{

    private LoggerInterface $securityLogger;

    private RequestStack $requestStack;


    public function __construct(
        LoggerInterface $securityLogger,
        RequestStack    $requestStack,

    )
    {

        $this->requestStack = $requestStack;
        $this->securityLogger = $securityLogger;


    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => 'onLoginFailure',
            LoginSuccessEvent::class => 'onLoginSuccess',
            LogoutEvent::class => 'onLogoutEvent',


        ];

    }

    public function onLoginFailure(): void
    {
        $emailEntered = $this->requestStack->getCurrentRequest()?->get('email');

        $clientIp = $this->getUserInfo()['clientIp'];
        $route = $this->getUserInfo()['route'];


        $this->securityLogger->info("AUTHENTIFICATION FAILLED : Authentification échoué avec l'adresse email : '$emailEntered' et l'ip : '$clientIp' venant de la route : '$route");


    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $userEmail = $event->getAuthenticatedToken()->getUserIdentifier();

        $clientIp = $this->getUserInfo()['clientIp'];
        $route = $this->getUserInfo()['route'];

        $this->securityLogger->info("AUTHENTIFICATION SUCCESS : Authentification réussi avec l'adresse email : '$userEmail' et l'ip : '$clientIp' venant de la route : '$route'");
    }


    public function onLogoutEvent(LogoutEvent $event): void
    {

        $userEmail = $event->getToken()?->getUserIdentifier();

        $clientIp = $this->getUserInfo()['clientIp'];
        $route = $this->getUserInfo()['route'];


        $this->securityLogger->info("AUTHENTIFICATION LOGOUT : déconnexion de l'utilisateur avec l'adresse email: '$userEmail' et l'ip : '$clientIp' venant de la route : '$route' ");

    }

    /**
     * @return array<mixed>
     *
     */
    public function getUserInfo(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        return [
            'clientIp' => $request?->getClientIp() ?? 'inconnu',
            'route' => $request?->attributes->get('_route') ?? 'inconnu',
        ];
    }


}
