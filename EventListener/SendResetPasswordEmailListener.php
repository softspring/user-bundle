<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\UserBundle\Mailer\MailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\SfsUserEvents;
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
     * SendResetPasswordEmailListener constructor.
     * @param MailerInterface $mailer
     * @param UserManagerInterface $userManager
     */
    public function __construct(MailerInterface $mailer, UserManagerInterface $userManager)
    {
        $this->mailer = $mailer;
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            SfsUserEvents::RESET_REQUEST_DO_REQUEST => [
                ['sendResetEmail', 0],
            ]
        ];
    }

    public function sendResetEmail(GetResponseFormEvent $event)
    {
        $user = $this->userManager->findUserByEmail($event->getForm()->get('email')->getData());

        if ($user instanceof UserInterface) {
            $this->mailer->sendResettingEmail($user);
        }
    }
}