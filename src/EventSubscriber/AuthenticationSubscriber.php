<?php


namespace App\EventSubscriber;


use App\Repository\AuthenticationLogRepository;
use App\Security\BrutForceChecker;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AuthenticationSubscriber implements EventSubscriberInterface
{

    private LoggerInterface $securityLogger;
    private RequestStack $requestStack;
    private BrutForceChecker $brutForceChecker;
    private AuthenticationLogRepository $authLogRepository;
    private SessionInterface $session;


    public function __construct(
        LoggerInterface  $securityLogger,
        RequestStack     $requestStack,
        BrutForceChecker $brutForceChecker,
        AuthenticationLogRepository $authLogRepository,
        SessionInterface $session
    )
    {

        $this->requestStack = $requestStack;
        $this->securityLogger = $securityLogger;
        $this->brutForceChecker = $brutForceChecker;
        $this->authLogRepository = $authLogRepository;
        $this->session = $session;


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
        $oauthProvider = $this->session->get('oauthProvider');

        $oauthProvider !== null ? $oauth = true : $oauth = false;

        $emailEntered = $this->requestStack->getCurrentRequest()?->get('email');


        $clientIp = $this->getUserInfo()['clientIp'];
        $route = $this->getUserInfo()['route'];


        $this->securityLogger->info("'AUTHENTIFICATION FAILLED' : Authentification échoué avec l'adresse email : '$emailEntered' et l'ip : '$clientIp' venant de la route : '$route");

        $this->brutForceChecker->addAuthenticationFailure($clientIp, $emailEntered, $oauth, $oauthProvider);

    }



    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
            $oauthProvider = $this->session->get('oauthProvider');

            $oauthProvider !== null ? $oauth = true : $oauth = false;

        $userEmail = $event->getAuthenticatedToken()->getUserIdentifier();

        $clientIp = $this->getUserInfo()['clientIp'];
        $route = $this->getUserInfo()['route'];

        $this->securityLogger->info("'AUTHENTIFICATION SUCCESS' : Authentification réussi avec l'adresse email : '$userEmail' et l'ip : '$clientIp' venant de la route : '$route'");
        $this->authLogRepository->addAuthenticationSuccess($clientIp, $userEmail, $oauth, $oauthProvider);
    }

    public function onLogoutEvent(LogoutEvent $event): void
    {

        $userEmail = $event->getToken()?->getUserIdentifier();

        $clientIp = $this->getUserInfo()['clientIp'];
        $route = $this->getUserInfo()['route'];



        $this->securityLogger->info("'AUTHENTIFICATION LOGOUT' : déconnexion de l'utilisateur avec l'adresse email: '$userEmail' et l'ip : '$clientIp' venant de la route : '$route' ");

    }

     /**
      * @return array<mixed>
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
