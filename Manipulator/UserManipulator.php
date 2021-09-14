<?php

namespace Softspring\UserBundle\Manipulator;

use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\EnablableInterface;
use Softspring\UserBundle\Model\UserAdminRolesInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserPasswordInterface;
use Softspring\UserBundle\Model\UserRolesInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
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
        /** @var UserInterface $user */
        $user = $this->userManager->createEntity();

        $user->setUsername($username);

        if ($user instanceof UserWithEmailInterface) {
            $user->setEmail($email);
        }

        if ($user instanceof UserPasswordInterface) {
            $user->setPlainPassword($plainPassword);
        }

        if ($user instanceof UserRolesInterface) {
            $user->setRoles($roles);
        }

        if ($user instanceof EnablableInterface) {
            $user->setEnabled($enabled);
        }

        if ($user instanceof UserAdminRolesInterface) {
            $user->setAdmin($admin);
            $user->setSuperAdmin($superAdmin);
        }

        $this->userManager->saveEntity($user);

        $event = new UserEvent($user, $this->requestStack->getCurrentRequest());
        $this->eventDispatcher->dispatch($event, SfsUserEvents::USER_CREATED);

        return $user;
    }
}