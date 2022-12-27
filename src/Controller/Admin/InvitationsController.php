<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
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

    public function resendEmail($invitation): Response
    {
        $invitation = $this->invitationsManager->findInvitationBy(['id' => $invitation]);

        if (!$invitation->getAcceptedAt() && $this->userMailer) {
            $this->userMailer->sendInvitationEmail($invitation);
        }

        return $this->redirectToRoute('sfs_user_admin_invitations_details', ['invitation' => $invitation]);
    }
}
