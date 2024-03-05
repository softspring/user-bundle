<?php

namespace Softspring\UserBundle\Manipulator;

use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Model\EnablableInterface;
use Softspring\UserBundle\Model\RolesInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;
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

        if ($invitation instanceof UserIdentifierUsernameInterface) {
            $invitation->setUsername($username);
        }

        $invitation->setEmail($email);

        if ($invitation instanceof EnablableInterface) {
            $invitation->setEnabled(false);
        }

        $invitation->setInvitationToken($this->tokenGenerator->generateToken());

        if ($invitation instanceof RolesInterface) {
            $invitation->setRoles($roles);
        }

        $this->userInvitationManager->saveEntity($invitation);

        $event = new UserInvitationEvent($invitation, $this->requestStack->getCurrentRequest());
        $this->eventDispatcher->dispatch($event, SfsUserEvents::USER_INVITED);

        return $invitation;
    }
}
