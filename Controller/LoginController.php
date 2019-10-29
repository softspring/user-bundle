<?php

namespace Softspring\UserBundle\Controller;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Form\LoginFormInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;

class LoginController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var LoginFormInterface
     */
    protected $loginForm;

    /**
     * @var array
     */
    protected $oauthServices;

    /**
     * LoginController constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoginFormInterface       $loginForm
     * @param array                    $oauthServices
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, LoginFormInterface $loginForm, array $oauthServices)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->loginForm = $loginForm;
        $this->oauthServices = $oauthServices;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function login(Request $request): Response
    {
        /** @var $session Session */
        $session = $request->getSession();

        $form = $this->createForm(get_class($this->loginForm), [
            '_username' => $session->get(Security::LAST_USERNAME) ?? '',
            '_password' => '',
        ], [
            'action' => $this->generateUrl('sfs_user_login_check'),
        ]);

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $form->addError(new FormError($request->attributes->get(Security::AUTHENTICATION_ERROR)));
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $form->addError(new FormError($session->get(Security::AUTHENTICATION_ERROR)->getMessage()));
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::LOGIN_ATTEMPT, new GetResponseFormEvent($form, $request))) {
            return $response;
        }

        return $this->render('@SfsUser/login/login.html.twig', [
            'login_form' => $form->createView(),
            'oauth_services' => $this->oauthServices,
        ]);
    }

    public function check()
    {
        throw new \RuntimeException('Configure check path in your firewall\'s form_login block at security.yaml');
    }

    public function logout()
    {
        throw new \RuntimeException('Activate logout feature in your firewall at security.yaml');
    }
}