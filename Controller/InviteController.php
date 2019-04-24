<?php

namespace Softspring\UserBundle\Controller;

use Softspring\UserBundle\Event\FormEvent;
use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Form\AcceptInvitationFormInterface;
use Softspring\User\Manager\UserInvitationManagerInterface;
use Softspring\User\Manager\UserManagerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InviteController extends AbstractController
{
    use Traits\DispatchTrait;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var UserInvitationManagerInterface
     */
    protected $invitationManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var AcceptInvitationFormInterface
     */
    protected $acceptForm;

    /**
     * InviteController constructor.
     *
     * @param UserManagerInterface           $userManager
     * @param UserInvitationManagerInterface $invitationManager
     * @param EventDispatcherInterface       $eventDispatcher
     * @param AcceptInvitationFormInterface  $acceptForm
     */
    public function __construct(UserManagerInterface $userManager, UserInvitationManagerInterface $invitationManager, EventDispatcherInterface $eventDispatcher, AcceptInvitationFormInterface $acceptForm)
    {
        $this->userManager = $userManager;
        $this->invitationManager = $invitationManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->acceptForm = $acceptForm;
    }

    /**
     * @param string  $token
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function accept(string $token, Request $request): Response
    {
        $invitation = $this->invitationManager->findInvitationByToken($token);

        if (null === $invitation) {
            throw $this->createNotFoundException(sprintf('The user with invitation token "%s" does not exist', $token));
        }

        if ($invitation->getAcceptedAt()) {
            return $this->redirectToRoute('sfs_user_invite_success');
        }

        $user = $invitation->getUser() ?? $this->invitationManager->createUser($invitation);

        if ($response = $this->dispatchGetResponse(SfsUserEvents::INVITATION_ACCEPT, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        $form = $this->createForm(get_class($this->acceptForm), $user, [])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $this->eventDispatcher->dispatch(SfsUserEvents::INVITATION_FORM_VALID, $event);

                $invitation->setUser($user);
                $invitation->setAcceptedAt(new \DateTime('now'));
                $user->setEnabled(true);

                $this->userManager->save($user);
                $this->invitationManager->save($invitation);

                if ($response = $this->dispatchGetResponse(SfsUserEvents::INVITATION_ACCEPTED, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }

                return $this->redirectToRoute('sfs_user_invite_success');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::INVITATION_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        return $this->render('@SfsUser/invite/accept.html.twig', array(
            'accept_form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function success(Request $request): Response
    {
        return $this->render('@SfsUser/invite/success.html.twig', [

        ]);
    }
}