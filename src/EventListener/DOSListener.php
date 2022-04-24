<?php

namespace App\EventListener;


use App\Event\DOSEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class DOSListener
{

    private LoggerInterface $securityLogger;

    private RequestStack $requestStack;

    public function __construct(LoggerInterface $securityLogger, RequestStack $requestStack)
    {
        $this->securityLogger = $securityLogger;
        $this->requestStack = $requestStack;
    }


    /**
     * @param DOSEvent $event
     * @return void
     */
    public function onDOSEvent(DOSEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $clientIp = $request?->getClientIp() ?? 'inconnu';
        $route = $request?->attributes->get('_route') ?? 'inconnu';
        $userEmail = $event->getUser()->getUserIdentifier();


        $this->securityLogger->info("'DOS (MULTIPLE REQUEST TO SERVER)': Potentielle attaque DOS de l'utilisateur eyant l'adresse email ('$userEmail') ayant l'adresse ip ('$clientIp') venant de la route ('$route') ");

    }
}