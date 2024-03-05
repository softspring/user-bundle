<?php

namespace Softspring\UserBundle\Security;

use Softspring\UserBundle\Model\UserInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

class LoginManager
{
    protected FirewallMap $firewallMap;

    protected TokenStorageInterface $tokenStorage;

    protected UserCheckerInterface $userChecker;

    protected SessionAuthenticationStrategyInterface $sessionStrategy;

    protected RequestStack $requestStack;

    public function __construct(FirewallMap $firewallMap, TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker, SessionAuthenticationStrategyInterface $sessionStrategy, RequestStack $requestStack)
    {
        $this->firewallMap = $firewallMap;
        $this->tokenStorage = $tokenStorage;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->requestStack = $requestStack;
    }

    public function loginUser(Request $request, UserInterface $user, ?Response $response = null)
    {
        $this->userChecker->checkPreAuth($user);

        $token = $this->createToken($request, $user);
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->sessionStrategy->onAuthentication($request, $token);

            //            if (null !== $response && null !== $this->rememberMeService) {
            //                 $this->rememberMeService->loginSuccess($request, $response, $token);
            //            }
        }

        $this->tokenStorage->setToken($token);
    }

    protected function createToken(Request $request, UserInterface $user): UsernamePasswordToken
    {
        $firewallContext = $this->firewallMap->getFirewallConfig($request);
        $firewallName = $firewallContext->getName();

        return new UsernamePasswordToken($user, $firewallName, $user->getRoles());
    }
}
