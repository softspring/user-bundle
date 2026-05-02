<?php

namespace Softspring\UserBundle\GoogleIdentityPlatform;

use Softspring\UserBundle\Model\UserInterface;

final readonly class IdentityPlatformUserSyncResult
{
    public function __construct(
        public UserInterface $user,
        public bool $localUserCreated,
        public bool $newIdentityPlatformUser,
    ) {
    }
}
