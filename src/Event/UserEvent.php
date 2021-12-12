<?php

namespace Softspring\UserBundle\Event;

use Softspring\CoreBundle\Event\RequestEvent;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class UserEvent extends RequestEvent
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * UserEvent constructor.
     *
     * @param UserInterface $user
     * @param Request|null  $request
     */
    public function __construct(UserInterface $user, ?Request $request)
    {
        parent::__construct($request);
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
