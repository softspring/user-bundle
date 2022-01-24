<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class InvitationsController extends AbstractController
{
    /**
     * @var UserInvitationManagerInterface
     */
    protected $invitationsManager;

    /**
     * @var UserMailerInterface
     */
    protected $userMailer;

    /**
     * InvitationsController constructor.
     */
    public function __construct(UserInvitationManagerInterface $invitationsManager, UserMailerInterface $userMailer)
    {
        $this->invitationsManager = $invitationsManager;
        $this->userMailer = $userMailer;
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

        if (!$invitation->getAcceptedAt()) {
            $this->userMailer->sendInvitationEmail($invitation);
        }

        return $this->redirectToRoute('sfs_user_admin_invitations_details', ['invitation' => $invitation]);
    }
}
