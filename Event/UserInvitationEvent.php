<?php

namespace Softspring\UserBundle\Event;

use Softspring\CoreBundle\Event\RequestEvent;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\HttpFoundation\Request;

class UserInvitationEvent extends RequestEvent
{
    /**
     * @var UserInvitationInterface
     */
    protected $invitation;

    /**
     * UserInvitationEvent constructor.
     *
     * @param UserInvitationInterface $invitation
     * @param Request|null            $request
     */
    public function __construct(UserInvitationInterface $invitation, ?Request $request)
    {
        parent::__construct($request);
        $this->invitation = $invitation;
    }

    /**
     * @return UserInvitationInterface
     */
    public function getInvitation(): UserInvitationInterface
    {
        return $this->invitation;
    }
}
