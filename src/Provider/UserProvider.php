<?php

namespace Softspring\UserBundle\Provider;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface as SfsUserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    protected UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function loadUserByUsername($username): SymfonyUserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * @throws UserNotFoundException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userManager->findUserByIdentifier($identifier);

        if (!$user) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $identifier));
        }

        return $user;
    }

    public function refreshUser(SymfonyUserInterface $user): SymfonyUserInterface
    {
        if (!$user instanceof SfsUserInterface) {
            throw new UnsupportedUserException(sprintf('Expected an instance of Softspring\UserBundle\Model\UserInterface, but got "%s".', get_class($user)));
        }

        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getEntityClass(), get_class($user)));
        }

        if (null === $reloadedUser = $this->userManager->findUserByIdentifier($user->getUserIdentifier())) {
            throw new UserNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getUserIdentifier()));
        }

        return $reloadedUser;
    }

    public function supportsClass($class): bool
    {
        return $this->userManager->getEntityClass() === $class || is_subclass_of($class, $this->userManager->getEntityClass());
    }
}
