<?php

declare(strict_types=1);

namespace Softspring\UserBundle\GoogleIdentityPlatform;

final readonly class IdentityPlatformGoogleAuthenticationResult
{
    public function __construct(
        public IdentityPlatformGoogleUser $user,
        public IdentityPlatformTokens $tokens,
    ) {
    }
}
