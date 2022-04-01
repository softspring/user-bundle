<?php

namespace Softspring\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\Environment;

class ImpersonateBarListener implements EventSubscriberInterface
{
    protected AuthorizationCheckerInterface $authorizationChecker;

    protected Environment $twig;

    protected UrlGeneratorInterface $urlGenerator;

    protected array $impersonateBarConfig;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Environment $twig, UrlGeneratorInterface $urlGenerator, array $impersonateBarConfig)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->impersonateBarConfig = $impersonateBarConfig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -100],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$event->isMainRequest() || $request->isXmlHttpRequest()) {
            return;
        }

        if ($this->isImpersonated()) {
            $this->injectBar($response, $request);
        }
    }

    protected function isImpersonated(): bool
    {
        try {
            return $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') && $this->authorizationChecker->isGranted('IS_IMPERSONATOR');
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function injectBar(Response $response, Request $request): void
    {
        $content = $response->getContent();
        $pos = stripos($content, '<body');
        $posEnd = stripos($content, '>', $pos);

        if (false !== $posEnd) {
            $content = substr($content, 0, $posEnd+1).$this->renderBar().substr($content, $posEnd+1);
            $response->setContent($content);
        }
    }

    /**
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
