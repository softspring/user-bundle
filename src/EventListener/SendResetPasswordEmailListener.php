<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\CoreBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\PasswordRequestInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Util\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SendResetPasswordEmailListener implements EventSubscriberInterface
{
    protected UserMailerInterface $mailer;

    protected UserManagerInterface $userManager;

    protected TokenGenerator $tokenGenerator;

    /**
     * SendResetPasswordEmailListener constructor.
     */
    public function __construct(UserMailerInterface $mailer, UserManagerInterface $userManager, TokenGenerator $tokenGenerator)
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
        $this->userManager->saveEntity($user);
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
