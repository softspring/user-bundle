<?php

declare(strict_types=1);

namespace Softspring\UserBundle\GoogleIdentityPlatform;

final readonly class IdentityPlatformAuthenticationResult
{
    public function __construct(
        public IdentityPlatformUserSyncResult $syncResult,
        public IdentityPlatformTokens $tokens,
    ) {
    }
}
