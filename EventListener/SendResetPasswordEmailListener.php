<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\UserBundle\Mailer\MailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\PasswordRequestInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Util\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SendResetPasswordEmailListener implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var TokenGenerator
     */
    protected $tokenGenerator;

    /**
     * SendResetPasswordEmailListener constructor.
     * @param MailerInterface $mailer
     * @param UserManagerInterface $userManager
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(MailerInterface $mailer, UserManagerInterface $userManager, TokenGenerator $tokenGenerator)
    {
        $this->mailer = $mailer;
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            SfsUserEvents::RESET_REQUEST_FORM_VALID => [
                ['onResetRequestCheckToken', 0],
            ],
            SfsUserEvents::RESET_REQUEST_DO_REQUEST => [
                ['sendResetEmail', 0],
            ],
        ];
    }

    public function onResetRequestCheckToken(GetResponseFormEvent $event)
    {
        /** @var UserInterface|PasswordRequestInterface $user */
        $user = $this->userManager->findUserByEmail($event->getForm()->get('email')->getData());

        if (!$user instanceof PasswordRequestInterface) {
            return;
        }

        $user->setPasswordRequestToken($this->tokenGenerator->generateToken());
        $user->setPasswordRequestedAt(new \DateTime('now'));
        $this->userManager->save($user);
    }

    public function sendResetEmail(GetResponseFormEvent $event)
    {
        /** @var UserInterface|PasswordRequestInterface $user */
        $user = $this->userManager->findUserByEmail($event->getForm()->get('email')->getData());

        if ($user instanceof PasswordRequestInterface) {
            $this->mailer->sendResettingEmail($user);
        }
    }
}