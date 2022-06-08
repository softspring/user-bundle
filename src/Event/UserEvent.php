<?php

namespace Softspring\UserBundle\Event;

use Softspring\Component\Events\RequestEvent;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class UserEvent extends RequestEvent
{
    protected UserInterface $user;

    public function __construct(UserInterface $user, ?Request $request)
    {
        parent::__construct($request);
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
