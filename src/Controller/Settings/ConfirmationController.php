<?php

namespace Softspring\UserBundle\Controller\Settings;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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

class ConfirmationController extends AbstractController
{
    use DispatchGetResponseTrait;

    public function __construct(
        protected UserManagerInterface $userManager,
        protected ?UserMailerInterface $userMailer,
        protected EventDispatcherInterface $eventDispatcher,
        protected LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function resendConfirmation(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();
        if ($user instanceof ConfirmableInterface && !$user->isConfirmed() && $this->userMailer instanceof UserMailerInterface) {
            $eventName = SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_SUCCESS;
            try {
                $this->userMailer->sendRegisterConfirmationEmail($user);
            } catch (Exception $e) {
                $this->logger->error('Failed to resend confirmation email.', ['exception' => $e, 'user_identifier' => $user->getUserIdentifier()]);
                $eventName = SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_ERROR;
            }

            if (($response = $this->dispatchGetResponse($eventName, new GetResponseUserEvent($user, $request))) instanceof Response) {
                return $response;
            }
        }

        return $this->redirect($request->server->get('HTTP_REFERER') ?? $this->generateUrl('sfs_user_preferences'));
    }
}
