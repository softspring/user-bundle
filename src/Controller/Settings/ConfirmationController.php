<?php

namespace Softspring\UserBundle\Controller\Settings;

use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ConfirmationController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected UserManagerInterface $userManager;

    protected ?UserMailerInterface $userMailer;

    protected EventDispatcherInterface $eventDispatcher;
    protected ?FlashBagInterface $flashBag;

    public function __construct(UserManagerInterface $userManager, ?UserMailerInterface $userMailer, EventDispatcherInterface $eventDispatcher, ?FlashBagInterface $flashBag)
    {
        $this->userManager = $userManager;
        $this->userMailer = $userMailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->flashBag = $flashBag;
    }

    public function resendConfirmation(Request $request): Response
    {
        try {
            /** @var UserInterface $user */
            $user = $this->getUser();

            if ($user instanceof ConfirmableInterface && !$user->isConfirmed()) {
                $this->userMailer->sendRegisterConfirmationEmail($user);
                if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_SUCCESS, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }
            }

        } catch (\Exception $e) {
            if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_ERROR, new GetResponseUserEvent($user, $request))) {
                return $response;
            }
        }

        return $this->redirect($request->server->get('HTTP_REFERER') ?? $this->generateUrl('sfs_user_preferences'));
    }
}
