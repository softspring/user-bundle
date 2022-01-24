<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Util\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegistrationListener implements EventSubscriberInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var UserMailerInterface
     */
    protected $mailer;

    /**
     * @var TokenGenerator
     */
    protected $tokenGenerator;

    /**
     * UserRegistrationListener constructor.
     */
    public function __construct(UserManagerInterface $userManager, UserMailerInterface $mailer, TokenGenerator $tokenGenerator)
    {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SfsUserEvents::REGISTER_SUCCESS => ['onRegisterSendConfirmationEmail', 100],
        ];
    }

    public function onRegisterSendConfirmationEmail(GetResponseUserEvent $event)
    {
        $user = $event->getUser();
        if (!$user instanceof ConfirmableInterface) {
            return;
        }

        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $this->userManager->saveEntity($user);

        $this->mailer->sendRegisterConfirmationEmail($user);
    }
}
