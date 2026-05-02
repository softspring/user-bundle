<?php

namespace Softspring\UserBundle\Model;

interface GoogleIdentityPlatformAwareInterface
{
    public function getAuthSource(): ?string;

    public function setAuthSource(?string $authSource): void;

    public function getIdentityPlatformUserId(): ?string;

    public function setIdentityPlatformUserId(?string $identityPlatformUserId): void;

    public function getIdentityProvider(): ?string;

    public function setIdentityProvider(?string $identityProvider): void;

    public function getIdentityProviderUserId(): ?string;

    public function setIdentityProviderUserId(?string $identityProviderUserId): void;
}
