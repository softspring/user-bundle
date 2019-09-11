<?php

namespace Softspring\UserBundle\Provider;

use Softspring\UserBundle\Model\UserInterface as SfsUserInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * UserProvider constructor.
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @inheritdoc
     */
    public function loadUserByUsername($username)
    {
        $user = $this->getUser($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function refreshUser(SymfonyUserInterface $user)
    {
        if (!$user instanceof SfsUserInterface) {
            throw new UnsupportedUserException(sprintf('Expected an instance of Softspring\UserBundle\Model\UserInterface, but got "%s".', get_class($user)));
        }

        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getClass(), get_class($user)));
        }

        if (null === $reloadedUser = $this->userManager->findUserBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    /**
     * @inheritdoc
     */
    public function supportsClass($class)
    {
        return $this->userManager->getClass() === $class || is_subclass_of($class, $this->userManager->getClass());
    }

    /**
     * @inheritdoc
     */
    protected function getUser(string $usernameOrEmail)
    {
        return $this->userManager->findUserByUsernameOrEmail($usernameOrEmail);
    }
}