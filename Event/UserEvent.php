<?php

namespace Softspring\UserBundle\Event;

use Softspring\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

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
