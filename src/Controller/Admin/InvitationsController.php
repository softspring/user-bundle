<?php

namespace Softspring\UserBundle\Controller\Admin;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\UserBundle\Event\GetResponseUserInvitationEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationsController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected UserInvitationManagerInterface $invitationsManager;

    protected ?UserMailerInterface $userMailer;

    protected EventDispatcherInterface $eventDispatcher;

    protected LoggerInterface $logger;

    public function __construct(UserInvitationManagerInterface $invitationsManager, ?UserMailerInterface $userMailer, EventDispatcherInterface $eventDispatcher, ?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->invitationsManager = $invitationsManager;
        $this->userMailer = $userMailer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function pendingCountWidget(): Response
    {
        return $this->render('@SfsUser/admin/invitations/widget-pending-count.html.twig', [
            'pending' => $this->invitationsManager->getRepository()->count(['acceptedAt' => null]),
            'total' => $this->invitationsManager->getRepository()->count([]),
        ]);
    }

    public function resendEmail(mixed $invitation, Request $request): Response
    {
        $invitation = $this->invitationsManager->findInvitationBy(['id' => $invitation]);
        if (!$invitation) {
            throw $this->createNotFoundException('Invitation not found.');
        }

        if (!$invitation->getAcceptedAt() && $this->userMailer) {
            $eventName = SfsUserEvents::ADMIN_INVITATIONS_RESEND_SUCCESS;
            try {
                $this->userMailer->sendInvitationEmail($invitation);
            } catch (Exception $e) {
                $this->logger->error('Failed to resend invitation email.', ['exception' => $e, 'invitation_id' => $invitation->getId()]);
                $eventName = SfsUserEvents::ADMIN_INVITATIONS_RESEND_ERROR;
            }

            if ($response = $this->dispatchGetResponse($eventName, new GetResponseUserInvitationEvent($invitation, $request))) {
                return $response;
            }
        }

        return $this->redirectToRoute('sfs_user_admin_invitations_details', ['invitation' => $invitation]);
    }
}
