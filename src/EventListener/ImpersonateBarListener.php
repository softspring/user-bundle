<?php

namespace Softspring\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\Environment;

class ImpersonateBarListener implements EventSubscriberInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var array
     */
    protected $impersonateBarConfig;

    /**
     * ImpersonateBarListener constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param array $impersonateBarConfig
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Environment $twig, UrlGeneratorInterface $urlGenerator, array $impersonateBarConfig)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->impersonateBarConfig = $impersonateBarConfig;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -100],
        ];
    }

    /**
     * @param FilterResponseEvent|ResponseEvent $event
     * Note: in SF4 FilterResponseEvent, in SF5 ResponseEvent
     */
    public function onKernelResponse($event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$event->isMasterRequest() || $request->isXmlHttpRequest()) {
            return;
        }

        if ($this->isImpersonated()) {
            $this->injectBar($response, $request);
        }
    }

    /**
     * @return bool
     */
    protected function isImpersonated(): bool
    {
        try {
            if (Kernel::VERSION_ID > 50100) {
                return $this->authorizationChecker->isGranted('IS_IMPERSONATOR');
            } else {
                return $this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN');
            }
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param Response $response
     * @param Request $request
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function injectBar(Response $response, Request $request): void
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false !== $pos) {
            $content = substr($content, 0, $pos).$this->renderBar().substr($content, $pos);
            $response->setContent($content);
        }
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function renderBar(): string
    {
        $viewData = [
            'exit_route' => 'sfs_user_admin_users_list',
            'exit_route_params' => [
                $this->impersonateBarConfig['switch_parameter'] => '_exit',
            ],
        ];

        return "\n".str_replace("\n", '', $this->twig->render('@SfsUser/impersonated_bar.html.twig', $viewData))."\n";
    }
}