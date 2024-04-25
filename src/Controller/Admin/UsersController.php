<?php

namespace Softspring\UserBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\RolesAdminInterface;
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

    protected EntityManagerInterface $em;

    protected ?UserMailerInterface $userMailer;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $em, ?UserMailerInterface $userMailer, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->em = $em;
        $this->userMailer = $userMailer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function promoteAdmin(string $user, Request $request): Response
    {
        $user = $this->userManager->findUserBy(['id' => $user]);

        $this->denyAccessUnlessGranted('PERMISSION_SFS_USER_ADMIN_USERS_PROMOTE', $user);

        if (!$user instanceof RolesAdminInterface) {
            throw new Exception(sprintf('User %s class must implement %s to promoting admins', get_class($user), RolesAdminInterface::class));
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_PROMOTE_INITIALIZE, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        if (!$user->isAdmin()) {
            $user->setAdmin(true);
            $this->em->flush();
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_PROMOTE_SUCCESS, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        if ($this->isGranted('PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_LIST')) {
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
                $this->userMailer && $this->userMailer->sendRegisterConfirmationEmail($user);

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
