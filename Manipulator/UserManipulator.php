<?php

namespace Softspring\UserBundle\Manipulator;

use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserManipulator
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * UserManipulator constructor.
     *
     * @param UserManagerInterface     $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param RequestStack             $requestStack
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, RequestStack $requestStack)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $plainPassword
     * @param array  $roles
     * @param bool   $enabled
     * @param bool   $admin
     * @param bool   $superAdmin
     *
     * @return UserInterface
     */
    public function create(string $username, string $email, string $plainPassword, array $roles = [], bool $enabled = false, bool $admin = false, bool $superAdmin = false): UserInterface
    {
        $user = $this->userManager->create();

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($plainPassword);
        $user->setRoles($roles);
        $user->setEnabled($enabled);
        $user->setAdmin($admin);
        $user->setSuperAdmin($superAdmin);

        $this->userManager->save($user);

        $event = new UserEvent($user, $this->requestStack->getCurrentRequest());
        $this->eventDispatcher->dispatch($event, SfsUserEvents::USER_CREATED);

        return $user;
    }
}