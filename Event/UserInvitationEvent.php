<?php

namespace Softspring\UserBundle\Event;

use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class UserInvitationEvent extends Event
{
    /**
     * @var UserInvitationInterface
     */
    protected $invitation;

    /**
     * @var Request|null
     */
    protected $request;

    /**
     * UserInvitationEvent constructor.
     *
     * @param UserInvitationInterface $invitation
     * @param Request|null            $request
     */
    public function __construct(UserInvitationInterface $invitation, ?Request $request)
    {
        $this->invitation = $invitation;
        $this->request = $request;
    }

    /**
     * @return UserInvitationInterface
     */
    public function getInvitation(): UserInvitationInterface
    {
        return $this->invitation;
    }

    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }
}
