<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Manager;

use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformAuthenticationResult;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformTokens;
use Softspring\UserBundle\Model\UserInterface;

interface IdentityPlatformSessionManagerInterface
{
    public function authenticateGoogleCredential(string $requestUri, string $googleCredential): IdentityPlatformAuthenticationResult;

    public function loginWithGoogleCredential(string $requestUri, string $googleCredential): IdentityPlatformAuthenticationResult;

    public function registerWithGoogleCredential(string $requestUri, string $googleCredential): IdentityPlatformAuthenticationResult;

    public function loadUserFromIdToken(string $idToken): UserInterface;

    public function refreshTokens(string $refreshToken): IdentityPlatformTokens;
}
