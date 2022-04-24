<?php

namespace App\EventListener;


use Symfony\Component\Security\Http\Event\LogoutEvent;

class CustomLogoutListener
{


    /**
     * @param LogoutEvent $logoutEvent
     * @return void
     */
    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $logoutEvent): void
    {
        $logoutEvent->getRequest()->getSession()->set('password_confirmed', null);

    }
}