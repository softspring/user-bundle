<?php

namespace Softspring\UserBundle\Security;

use Softspring\User\Model\UserInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

class LoginManager
{
    /**
     * @var FirewallMap
     */
    protected $firewallMap;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var UserCheckerInterface
     */
    protected $userChecker;

    /**
     * @var SessionAuthenticationStrategyInterface
     */
    protected $sessionStrategy;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RememberMeServicesInterface
     */
    protected $rememberMeService;

    /**
     * LoginManager constructor.
     *
     * @param FirewallMap                            $firewallMap
     * @param TokenStorageInterface                  $tokenStorage
     * @param UserCheckerInterface                   $userChecker
     * @param SessionAuthenticationStrategyInterface $sessionStrategy
     * @param RequestStack                           $requestStack
     * @param null|RememberMeServicesInterface       $rememberMeService
     */
    public function __construct(FirewallMap $firewallMap, TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker, SessionAuthenticationStrategyInterface $sessionStrategy, RequestStack $requestStack, ?RememberMeServicesInterface $rememberMeService)
    {
        $this->firewallMap = $firewallMap;
        $this->tokenStorage = $tokenStorage;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->requestStack = $requestStack;
        $this->rememberMeService = $rememberMeService;
    }

    public function loginUser(Request $request, UserInterface $user, Response $response = null)
    {
        $this->userChecker->checkPreAuth($user);

        $token = $this->createToken($request, $user);
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->sessionStrategy->onAuthentication($request, $token);

            if (null !== $response && null !== $this->rememberMeService) {
                $this->rememberMeService->loginSuccess($request, $response, $token);
            }
        }

        $this->tokenStorage->setToken($token);
    }

    protected function createToken(Request $request, UserInterface $user): UsernamePasswordToken
    {
        $firewallContext = $this->firewallMap->getFirewallConfig($request);
        $firewallName = $firewallContext->getName();

        return new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
    }
}