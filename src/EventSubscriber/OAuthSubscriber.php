<?php

namespace App\EventSubscriber;

use App\Event\OAuthEvent;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OAuthSubscriber implements EventSubscriberInterface
{

    private LoggerInterface $OauthLogger;




    public function __construct(
        LoggerInterface $OauthLogger,
       )
    {
        $this->OauthLogger = $OauthLogger;

    }


    public static function getSubscribedEvents(): array
    {
        return [
            OAuthEvent::USER_CREATE_FROM_OAUTH => 'userCreateFromOAuth'
        ];
    }

    public function userCreateFromOAuth(OAuthEvent $event): void
    {
        $oauthProvider = $event->getOauthProvider();
        $email = $event->getOauthEmail();
        $ID = $event->getOauthID();
        $isVerified = !$event->getOauthAccountIsVerified()? 'non verified' : 'verified';
        $randomPassword = !$event->getRandomPassword()? '' : 'un mot de passe provisoire a était généré';



        $this->OauthLogger->info("Un utilisateur s'est connecté via un compte '$oauthProvider', '$isVerified' ayant l'identifiant '$ID' et l'adresse email '$email' $randomPassword");

    }


}