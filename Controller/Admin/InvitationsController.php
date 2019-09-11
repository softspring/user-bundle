<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\ExtraBundle\Controller\AbstractController;
use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Form\Admin\InvitationFormInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationsController extends AbstractController
{
    /**
     * @var UserInvitationManagerInterface
     */
    protected $invitationManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var InvitationFormInterface
     */
    protected $invitationForm;

    /**
     * @var TokenGeneratorInterface
     */
    protected $tokenGenerator;

    /**
     * InvitationsController constructor.
     *
     * @param UserInvitationManagerInterface $invitationManager
     * @param EventDispatcherInterface       $eventDispatcher
     * @param InvitationFormInterface        $invitationForm
     * @param TokenGeneratorInterface        $tokenGenerator
     */
    public function __construct(UserInvitationManagerInterface $invitationManager, EventDispatcherInterface $eventDispatcher, InvitationFormInterface $invitationForm, TokenGeneratorInterface $tokenGenerator)
    {
        $this->invitationManager = $invitationManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->invitationForm = $invitationForm;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function list(Request $request): Response
    {
        $repo = $this->invitationManager->getRepository();

        $invitations = $repo->findAll();

        return $this->render('@SfsUser/admin/invitations/list.html.twig', [
            'invitations' => $invitations,
        ]);
    }

    /**
     * @param string  $invitation
     * @param Request $request
     *
     * @return Response
     */
    public function details(string $invitation, Request $request): Response
    {
        $invitation = $this->invitationManager->findInvitationBy(['id' => $invitation]);

        return $this->render('@SfsUser/admin/invitations/details.html.twig', [
            'invitation' => $invitation,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $invitation = $this->invitationManager->create();

        $form = $this->createForm(get_class($this->invitationForm), $invitation, ['method' => 'POST'])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $invitation->setInvitationToken($this->tokenGenerator->generateToken());
                $this->invitationManager->save($invitation);

                $this->eventDispatcher->dispatch(new UserInvitationEvent($invitation, $request), SfsUserEvents::USER_INVITED);

                return $this->redirectToRoute('sfs_user_admin_invitations_list');
            }
        }

        return $this->render('@SfsUser/admin/invitations/create.html.twig', [
            'create_form' => $form->createView(),
        ]);
    }
}