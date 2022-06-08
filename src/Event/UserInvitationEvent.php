<?php

namespace Softspring\UserBundle\Event;

use Softspring\Component\Events\RequestEvent;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\HttpFoundation\Request;

class UserInvitationEvent extends RequestEvent
{
    protected UserInvitationInterface $invitation;

    public function __construct(UserInvitationInterface $invitation, ?Request $request)
    {
        parent::__construct($request);
        $this->invitation = $invitation;
    }

    public function getInvitation(): UserInvitationInterface
    {
        return $this->invitation;
    }
}
