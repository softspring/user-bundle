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
    /**
     * @var UserInvitationManagerInterface
     */
    protected $userInvitationManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var TokenGeneratorInterface
     */
    protected $tokenGenerator;

    /**
     * UserInvitationManipulator constructor.
     *
     * @param UserInvitationManagerInterface $userInvitationManager
     * @param EventDispatcherInterface       $eventDispatcher
     * @param RequestStack                   $requestStack
     * @param TokenGeneratorInterface        $tokenGenerator
     */
    public function __construct(UserInvitationManagerInterface $userInvitationManager, EventDispatcherInterface $eventDispatcher, RequestStack $requestStack, TokenGeneratorInterface $tokenGenerator)
    {
        $this->userInvitationManager = $userInvitationManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param string      $email
     * @param string|null $username
     * @param array       $roles
     *
     * @return UserInvitationInterface
     */
    public function invite(string $email, ?string $username = null, array $roles = []): UserInvitationInterface
    {
        $invitation = $this->userInvitationManager->create();
        $invitation->setUsername($username);
        $invitation->setEmail($email);
        $invitation->setEnabled(false);
        $invitation->setInvitationToken($this->tokenGenerator->generateToken());
        $invitation->setRoles($roles);

        $this->userInvitationManager->save($invitation);

        $event = new UserInvitationEvent($invitation, $this->requestStack->getCurrentRequest());
        $this->eventDispatcher->dispatch(SfsUserEvents::USER_INVITED, $event);

        return $invitation;
    }
}