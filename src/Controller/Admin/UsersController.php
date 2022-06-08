<?php

namespace Softspring\UserBundle\Controller\Admin;

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
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class UsersController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected UserManagerInterface $userManager;

    protected UserMailerInterface $userMailer;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(UserManagerInterface $userManager, UserMailerInterface $userMailer, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->userMailer = $userMailer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function promoteAdmin(string $user, Request $request): Response
    {
        $user = $this->userManager->findUserBy(['id' => $user]);

        $this->denyAccessUnlessGranted('ROLE_ADMIN_USERS_PROMOTE', $user);

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_PROMOTE_INITIALIZE, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        if (!$user->isAdmin()) {
            $user->setAdmin(true);
            $this->getDoctrine()->getManager()->flush();
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_PROMOTE_SUCCESS, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        if ($this->isGranted('ROLE_ADMIN_ADMINISTRATORS_LIST')) {
            return $this->redirectToRoute('sfs_user_admin_administrators_list');
        } else {
            return $this->redirectToRoute('sfs_user_admin_users_list');
        }
    }

    public function usersCountWidget(): Response
    {
        return $this->render('@SfsUser/admin/users/widget-users-count.html.twig', [
            'users' => $this->userManager->getRepository()->count(['admin' => 0]),
            'administrators' => $this->userManager->getRepository()->count(['admin' => 1]),
            'total' => $this->userManager->getRepository()->count([]),
        ]);
    }

    public function usersPendingConfirmCountWidget(): Response
    {
        return $this->render('@SfsUser/admin/users/widget-pending-confirm-count.html.twig', [
            'count' => $this->userManager->getRepository()->count(['confirmedAt' => null]),
        ]);
    }

    public function resendConfirmationEmail(string $user, Request $request): Response
    {
        /** @var ConfirmableInterface|UserInterface $user */
        $user = $this->userManager->findUserBy(['id' => $user]);

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_INITIALIZE, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        if (!$user->isConfirmed()) {
            try {
                $this->userMailer->sendRegisterConfirmationEmail($user);

                if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_SUCCESS, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }
            } catch (TransportExceptionInterface $e) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_ERROR, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }
            }
        } else {
            if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_ALREADY_CONFIRMED, new GetResponseUserEvent($user, $request))) {
                return $response;
            }
        }

        return $this->redirectToRoute('sfs_user_admin_users_details', ['user' => $user]);
    }
}
