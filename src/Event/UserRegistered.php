<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserRegistered extends Event
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUserId()
    {
        return $this->user->id;
    }

    public function getUserEmail()
    {
        return $this->user->email;
    }
}
