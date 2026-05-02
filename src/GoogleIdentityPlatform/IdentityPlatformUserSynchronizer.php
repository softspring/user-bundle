<?php

namespace Softspring\UserBundle\GoogleIdentityPlatform;

use DateTime;
use RuntimeException;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\GoogleIdentityPlatformAwareInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserLastLoginInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;

/* added-by-ea-tests final */ class IdentityPlatformUserSynchronizer
{
    public function __construct(
        private readonly UserManagerInterface $userManager,
    ) {
    }

    public function sync(IdentityPlatformGoogleUser $identityUser): IdentityPlatformUserSyncResult
    {
        $this->assertSupportedUserClass();

        $user = $this->findExistingUser($identityUser);
        $localUserCreated = false;

        if (!$user instanceof UserInterface) {
            /** @var UserInterface $user */
            $user = $this->userManager->createEntity();
            $localUserCreated = true;
        }

        if (!$user instanceof GoogleIdentityPlatformAwareInterface) {
            throw new RuntimeException(sprintf('Configured user class "%s" must implement %s.', $user::class, GoogleIdentityPlatformAwareInterface::class));
        }

        if ($user instanceof UserWithEmailInterface && $identityUser->email) {
            $user->setEmail($identityUser->email);
        }

        if ($user instanceof NameSurnameInterface) {
            if ($identityUser->firstName || !$user->getName()) {
                $user->setName($identityUser->firstName ?? $this->extractFirstName($identityUser->displayName));
            }

            if ($identityUser->lastName || !$user->getSurname()) {
                $user->setSurname($identityUser->lastName ?? $this->extractLastName($identityUser->displayName));
            }
        }

        $user->setAuthSource('identity_platform');
        $user->setIdentityPlatformUserId($identityUser->identityPlatformUserId);
        $user->setIdentityProvider($identityUser->identityProvider);
        $user->setIdentityProviderUserId($identityUser->identityProviderUserId);

        if (method_exists($user, 'setAvatarUrl')) {
            $user->setAvatarUrl($identityUser->photoUrl);
        }

        if ($user instanceof UserLastLoginInterface) {
            $user->setLastLogin(new DateTime());
        }

        if ($user instanceof ConfirmableInterface && $identityUser->emailVerified && !$user->isConfirmed()) {
            $user->setConfirmedAt(new DateTime());
        }

        $this->userManager->saveEntity($user);

        return new IdentityPlatformUserSyncResult(
            user: $user,
            localUserCreated: $localUserCreated,
            newIdentityPlatformUser: $identityUser->isNewIdentityPlatformUser,
        );
    }

    private function findExistingUser(IdentityPlatformGoogleUser $identityUser): ?UserInterface
    {
        $user = $this->userManager->findUserBy(['identityPlatformUserId' => $identityUser->identityPlatformUserId]);

        if ($user || !$identityUser->email || !$this->supportsEmailLookup()) {
            return $user;
        }

        return $this->userManager->findUserBy(['email' => $identityUser->email]);
    }

    private function assertSupportedUserClass(): void
    {
        $userClass = $this->userManager->getEntityClass();

        if (!is_a($userClass, GoogleIdentityPlatformAwareInterface::class, true)) {
            throw new RuntimeException(sprintf('Configured user class "%s" must implement %s to use Google Identity Platform login.', $userClass, GoogleIdentityPlatformAwareInterface::class));
        }
    }

    private function supportsEmailLookup(): bool
    {
        return is_a($this->userManager->getEntityClass(), UserWithEmailInterface::class, true);
    }

    private function extractFirstName(?string $displayName): ?string
    {
        if (!$displayName) {
            return null;
        }

        $parts = preg_split('/\s+/', trim($displayName)) ?: [];

        return $parts[0] ?? null;
    }

    private function extractLastName(?string $displayName): ?string
    {
        if (!$displayName) {
            return null;
        }

        $parts = preg_split('/\s+/', trim($displayName)) ?: [];

        if (count($parts) < 2) {
            return null;
        }

        array_shift($parts);

        return implode(' ', $parts);
    }
}
