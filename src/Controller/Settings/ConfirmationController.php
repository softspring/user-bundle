<?php

namespace Softspring\UserBundle\Controller\Settings;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfirmationController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var UserMailerInterface
     */
    protected $userMailer;

    /**
     * ConfirmationController constructor.
     *
     * @param UserManagerInterface $userManager
     * @param UserMailerInterface  $userMailer
     */
    public function __construct(UserManagerInterface $userManager, UserMailerInterface $userMailer)
    {
        $this->userManager = $userManager;
        $this->userMailer = $userMailer;
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