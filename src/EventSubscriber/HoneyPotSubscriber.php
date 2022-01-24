<?php


namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HoneyPotSubscriber implements EventSubscriberInterface
{
     private LoggerInterface $securityLogger;

     private RequestStack $requestStack;

    public function __construct(
        LoggerInterface $securityLogger,
        RequestStack $requestStack
    )
    {
        $this->securityLogger = $securityLogger;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
       return [
           FormEvents::PRE_SUBMIT => 'checkHoneyPot'
       ];
    }

    public function checkHoneyPot(FormEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if(!$request){
            return;
        }
        $form = $event->getForm();

        $data = $event->getData();

        if (!array_key_exists('adresse-HP', $data) || !array_key_exists('ville-HP', $data)) {
            throw new HttpException(400);
        }

        [
            'adresse-HP'  => $adress,
            'ville-HP'    => $city
        ] = $data;

        if ($adress !== "" || $city !== "") {
           $this->securityLogger->info("'AUTHENTIFICATION SECURITY' : Une potentielle tentative de création de compte via un robot spammer avec l'adresse IP suivante '{$request->getClientIp()}' a eu lieu
            les champs 'adresse' et 'ville' on été rempli avec les données suivante ('{$adress}', '{$city}') ");
           throw new HttpException(403);
        }
    }
}