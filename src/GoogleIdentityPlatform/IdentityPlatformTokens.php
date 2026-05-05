<?php

declare(strict_types=1);

namespace Softspring\UserBundle\GoogleIdentityPlatform;

final readonly class IdentityPlatformTokens
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
        public int $expiresIn,
    ) {
    }
}
