<?php

namespace Softspring\UserBundle\Controller;

use RuntimeException;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformGoogleAuthenticator;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformUserSynchronizer;
use Softspring\UserBundle\Security\LoginManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class GoogleIdentityPlatformLoginController extends AbstractController
{
    public function __construct(
        private readonly array $googleIdentityPlatformConfig,
        private readonly LoginManager $loginManager,
    ) {
    }

    public function authenticate(
        Request $request,
        IdentityPlatformGoogleAuthenticator $googleAuthenticator,
        IdentityPlatformUserSynchronizer $userSynchronizer,
    ): RedirectResponse {
        if (!$this->googleIdentityPlatformConfig['enabled']) {
            $this->addFlash('sfs_user_google_identity_platform_error', 'Google sign-in is disabled.');

            return $this->redirectToConfiguredRoute('failure_route');
        }

        $cookieToken = $request->cookies->get('g_csrf_token');
        $bodyToken = $request->request->get('g_csrf_token');
        $credential = $request->request->get('credential');

        if (!is_string($cookieToken) || !is_string($bodyToken) || '' === $cookieToken || !hash_equals($cookieToken, $bodyToken)) {
            $this->addFlash('sfs_user_google_identity_platform_error', 'Invalid Google sign-in CSRF token.');

            return $this->redirectToConfiguredRoute('failure_route');
        }

        if (!is_string($credential) || '' === $credential) {
            $this->addFlash('sfs_user_google_identity_platform_error', 'Missing Google sign-in credential.');

            return $this->redirectToConfiguredRoute('failure_route');
        }

        try {
            $identityUser = $googleAuthenticator->authenticate($request->getUri(), $credential);
            $result = $userSynchronizer->sync($identityUser);
        } catch (RuntimeException $exception) {
            $this->addFlash('sfs_user_google_identity_platform_error', $exception->getMessage());

            return $this->redirectToConfiguredRoute('failure_route');
        }

        $this->loginManager->loginUser($request, $result->user);

        return $this->redirectToConfiguredRoute('success_route');
    }

    private function redirectToConfiguredRoute(string $routeKey): RedirectResponse
    {
        $routeName = $this->googleIdentityPlatformConfig[$routeKey] ?? null;
        $routeParams = $this->googleIdentityPlatformConfig["{$routeKey}_params"] ?? [];

        if (is_string($routeName) && '' !== $routeName) {
            return $this->redirectToRoute($routeName, is_array($routeParams) ? $routeParams : []);
        }

        return $this->redirect('/');
    }
}
