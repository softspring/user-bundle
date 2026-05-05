<?php

namespace Softspring\UserBundle\Security;

use SensitiveParameter;
use Softspring\UserBundle\Manager\IdentityPlatformSessionManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class IdentityPlatformAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly IdentityPlatformSessionManagerInterface $identityPlatformSessionManager,
    ) {
    }

    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        $user = $this->identityPlatformSessionManager->loadUserFromIdToken($accessToken);

        return new UserBadge($user->getUserIdentifier(), static fn (): UserInterface => $user);
    }
}
