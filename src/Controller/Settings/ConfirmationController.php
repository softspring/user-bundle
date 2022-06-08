<?php

namespace Softspring\UserBundle\Controller\Settings;

use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfirmationController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected UserManagerInterface $userManager;

    protected UserMailerInterface $userMailer;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(UserManagerInterface $userManager, UserMailerInterface $userMailer, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->userMailer = $userMailer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function resendConfirmation(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if ($user instanceof ConfirmableInterface && !$user->isConfirmed()) {
            $this->userMailer->sendRegisterConfirmationEmail($user);
        }

        return $this->redirect($request->server->get('HTTP_REFERER') ?? $this->generateUrl('sfs_user_preferences'));
    }
}
