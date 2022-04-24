<?php

namespace App\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class DOSEvent extends Event
{

    public const TOO_REQUEST_TO_SERVER = 'DOS.event';


    private UserInterface $user;


    public function __construct(UserInterface $user)
    {
        $this->user = $user;

    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }


}