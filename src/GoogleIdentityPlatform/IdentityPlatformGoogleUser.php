<?php

namespace Softspring\UserBundle\GoogleIdentityPlatform;

final readonly class IdentityPlatformGoogleUser
{
    public function __construct(
        public string $identityPlatformUserId,
        public string $identityProvider,
        public string $identityProviderUserId,
        public ?string $email,
        public bool $emailVerified,
        public ?string $displayName,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $photoUrl,
        public bool $isNewIdentityPlatformUser,
        public ?string $tenantId,
    ) {
    }
}
