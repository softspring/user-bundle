<?php

namespace Softspring\UserBundle\Manipulator;

use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserInvitationManipulator
{
    protected UserInvitationManagerInterface $userInvitationManager;

    protected EventDispatcherInterface $eventDispatcher;

    protected RequestStack $requestStack;

    protected TokenGeneratorInterface $tokenGenerator;

    /**
     * UserInvitationManipulator constructor.
     */
    public function __construct(UserInvitationManagerInterface $userInvitationManager, EventDispatcherInterface $eventDispatcher, RequestStack $requestStack, TokenGeneratorInterface $tokenGenerator)
    {
        $this->userInvitationManager = $userInvitationManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function invite(string $email, ?string $username = null, array $roles = []): UserInvitationInterface
    {
        $invitation = $this->userInvitationManager->createEntity();
        $invitation->setUsername($username);
        $invitation->setEmail($email);
        $invitation->setEnabled(false);
        $invitation->setInvitationToken($this->tokenGenerator->generateToken());
        $invitation->setRoles($roles);

        $this->userInvitationManager->saveEntity($invitation);

        $event = new UserInvitationEvent($invitation, $this->requestStack->getCurrentRequest());
        $this->eventDispatcher->dispatch(SfsUserEvents::USER_INVITED, $event);

        return $invitation;
    }
}
