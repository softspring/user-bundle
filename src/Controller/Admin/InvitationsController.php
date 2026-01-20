<?php

namespace Softspring\UserBundle\Controller\Admin;

use http\Client\Request;
use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class InvitationsController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected UserInvitationManagerInterface $invitationsManager;

    protected ?UserMailerInterface $userMailer;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(UserInvitationManagerInterface $invitationsManager, ?UserMailerInterface $userMailer, EventDispatcherInterface $eventDispatcher)
    {
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
        try {
            $invitation = $this->invitationsManager->findInvitationBy(['id' => $invitation]);
            $user = $invitation->getUser();

            if (!$invitation->getAcceptedAt() && $this->userMailer) {
                $this->userMailer->sendInvitationEmail($invitation);
                if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_INVITATIONS_RESEND_SUCCESS, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }
            }
        } catch (\Exception $e) {
            if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_INVITATIONS_RESEND_ERROR, new GetResponseUserEvent($user, $request))) {
                return $response;
            }
        }


        return $this->redirectToRoute('sfs_user_admin_invitations_details', ['invitation' => $invitation]);
    }
}
