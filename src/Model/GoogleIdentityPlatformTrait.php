<?php

namespace Softspring\UserBundle\Model;

trait GoogleIdentityPlatformTrait
{
    protected ?string $authSource = null;

    protected ?string $identityPlatformUserId = null;

    protected ?string $identityProvider = null;

    protected ?string $identityProviderUserId = null;

    public function getAuthSource(): ?string
    {
        return $this->authSource;
    }

    public function setAuthSource(?string $authSource): void
    {
        $this->authSource = $authSource;
    }

    public function getIdentityPlatformUserId(): ?string
    {
        return $this->identityPlatformUserId;
    }

    public function setIdentityPlatformUserId(?string $identityPlatformUserId): void
    {
        $this->identityPlatformUserId = $identityPlatformUserId;
    }

    public function getIdentityProvider(): ?string
    {
        return $this->identityProvider;
    }

    public function setIdentityProvider(?string $identityProvider): void
    {
        $this->identityProvider = $identityProvider;
    }

    public function getIdentityProviderUserId(): ?string
    {
        return $this->identityProviderUserId;
    }

    public function setIdentityProviderUserId(?string $identityProviderUserId): void
    {
        $this->identityProviderUserId = $identityProviderUserId;
    }
}
