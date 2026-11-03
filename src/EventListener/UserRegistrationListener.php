<?php

namespace Softspring\UserBundle\EventListener;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Util\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegistrationListener implements EventSubscriberInterface
{
    protected UserManagerInterface $userManager;

    protected UserMailerInterface $mailer;

    protected TokenGenerator $tokenGenerator;

    protected LoggerInterface $logger;

    public function __construct(UserManagerInterface $userManager, UserMailerInterface $mailer, TokenGenerator $tokenGenerator, ?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SfsUserEvents::REGISTER_SUCCESS => ['onRegisterSendConfirmationEmail', 100],
        ];
    }

    public function onRegisterSendConfirmationEmail(GetResponseUserEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof ConfirmableInterface) {
            return;
        }

        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $this->userManager->saveEntity($user);

        try {
            $this->mailer->sendRegisterConfirmationEmail($user);
        } catch (Exception $e) {
            $this->logger->error('Failed to send registration confirmation email.', ['exception' => $e, 'user_identifier' => $user->getUserIdentifier()]);
        }
    }
}
