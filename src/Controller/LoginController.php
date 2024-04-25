<?php

namespace Softspring\UserBundle\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\Component\Events\GetResponseFormEvent;
use Softspring\UserBundle\Form\LoginFormInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected LoginFormInterface $loginForm;

    protected array $oauthServices;

    protected ?string $targetPathParameter;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(LoginFormInterface $loginForm, array $oauthServices, ?string $targetPathParameter, EventDispatcherInterface $eventDispatcher)
    {
        $this->loginForm = $loginForm;
        $this->oauthServices = $oauthServices;
        $this->targetPathParameter = $targetPathParameter;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function login(Request $request, TranslatorInterface $translator): Response
    {
        /** @var Session $session */
        $session = $request->getSession();

        $loginCheckParams = [];
        if ($this->targetPathParameter && $targetPath = $request->get($this->targetPathParameter)) {
            $loginCheckParams[$this->targetPathParameter] = $targetPath;
        }

        $authenticationErrorKey = class_exists('Symfony\Component\Security\Http\SecurityRequestAttributes') ? constant('Symfony\Component\Security\Http\SecurityRequestAttributes::AUTHENTICATION_ERROR') : (class_exists('Symfony\Component\Security\Core\Security') ? constant('Symfony\Component\Security\Core\Security::AUTHENTICATION_ERROR') : null);
        $lastUserNameKey = class_exists('Symfony\Component\Security\Http\SecurityRequestAttributes') ? constant('Symfony\Component\Security\Http\SecurityRequestAttributes::LAST_USERNAME') : (class_exists('Symfony\Component\Security\Core\Security') ? constant('Symfony\Component\Security\Core\Security::LAST_USERNAME') : null);

        $form = $this->createForm(get_class($this->loginForm), [
            '_username' => $session->get($lastUserNameKey) ?? '',
            '_password' => '',
        ], [
            'action' => $this->generateUrl('sfs_user_login_check', $loginCheckParams),
        ]);

        if ($request->attributes->has($authenticationErrorKey)) {
            $form->addError(new FormError($request->attributes->get($authenticationErrorKey)));
        } elseif ($session->has($authenticationErrorKey)) {
            $error = $session->get($authenticationErrorKey);

            if ($error instanceof TooManyLoginAttemptsAuthenticationException) {
                $form->addError(new FormError($translator->trans($error->getMessageKey(), $error->getMessageData(), 'security')));
            } else {
                $form->addError(new FormError($session->get($authenticationErrorKey)->getMessage()));
            }

            $session->remove($authenticationErrorKey);
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::LOGIN_ATTEMPT, new GetResponseFormEvent($form, $request))) {
            return $response;
        }

        return $this->render('@SfsUser/login/login.html.twig', [
            'login_form' => $form->createView(),
            'oauth_services' => $this->oauthServices,
            'register_params' => $loginCheckParams,
        ]);
    }

    public function check()
    {
        throw new RuntimeException('Configure check path in your firewall\'s form_login block at security.yaml');
    }

    public function logout()
    {
        throw new RuntimeException('Activate logout feature in your firewall at security.yaml');
    }
}
