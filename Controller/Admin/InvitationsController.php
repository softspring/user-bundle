<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class InvitationsController extends AbstractController
{
    /**
     * @var UserInvitationManagerInterface
     */
    protected $invitationsManager;

    /**
     * InvitationsController constructor.
     *
     * @param UserInvitationManagerInterface $invitationsManager
     */
    public function __construct(UserInvitationManagerInterface $invitationsManager)
    {
        $this->invitationsManager = $invitationsManager;
    }

    public function pendingCountWidget(): Response
    {
        return $this->render('@SfsUser/admin/invitations/widget-pending-count.html.twig', [
            'pending' => $this->invitationsManager->getRepository()->count(['acceptedAt' => null]),
            'total' => $this->invitationsManager->getRepository()->count([]),
        ]);
    }
}