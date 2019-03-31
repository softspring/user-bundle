<?php

namespace Softspring\UserBundle\Event;

use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class UserEvent extends Event
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var Request|null
     */
    protected $request;

    /**
     * UserEvent constructor.
     *
     * @param UserInterface $user
     * @param Request|null  $request
     */
    public function __construct(UserInterface $user, ?Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }
}
