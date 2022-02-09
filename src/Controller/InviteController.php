<?php

namespace Softspring\UserBundle\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use Softspring\CoreBundle\Controller\Traits\DispatchGetResponseTrait;
use Softspring\CoreBundle\Event\FormEvent;
use Softspring\CoreBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Form\AcceptInvitationFormInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\EnablableInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InviteController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected UserManagerInterface $userManager;

    protected UserInvitationManagerInterface $invitationManager;

    protected AcceptInvitationFormInterface $acceptForm;

    protected EventDispatcherInterface $eventDispatcher;

    /**
     * InviteController constructor.
     */
    public function __construct(UserManagerInterface $userManager, UserInvitationManagerInterface $invitationManager, AcceptInvitationFormInterface $acceptForm, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->invitationManager = $invitationManager;
        $this->acceptForm = $acceptForm;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
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
                $this->dispatch(SfsUserEvents::INVITATION_FORM_VALID, $event);

                $invitation->setUser($user);
                $invitation->setAcceptedAt(new \DateTime('now'));

                if ($user instanceof EnablableInterface) {
                    $user->setEnabled(true);
                }

                $this->userManager->saveEntity($user);
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

        return $this->render('@SfsUser/invite/accept.html.twig', [
            'accept_form' => $form->createView(),
        ]);
    }

    public function success(Request $request): Response
    {
        return $this->render('@SfsUser/invite/success.html.twig', [
        ]);
    }
}
