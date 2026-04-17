<?php

namespace Softspring\UserBundle\Controller\Admin;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\EnablableInterface;
use Softspring\UserBundle\Model\RolesAdminInterface;
use Softspring\UserBundle\Model\User;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Util\TokenGeneratorInterface;
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

    protected TokenGeneratorInterface $tokenGenerator;

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $em, ?UserMailerInterface $userMailer, EventDispatcherInterface $eventDispatcher, TokenGeneratorInterface $tokenGenerator)
    {
        $this->userManager = $userManager;
        $this->em = $em;
        $this->userMailer = $userMailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function promoteAdmin(string $user, Request $request): Response
    {
        $user = $this->userManager->findUserBy(['id' => $user]);

        $this->denyAccessUnlessGranted('PERMISSION_SFS_USER_ADMIN_USERS_PROMOTE', $user);

        if (!$user instanceof RolesAdminInterface) {
            throw new Exception(sprintf('User %s class must implement %s to promoting admins', get_class($user), RolesAdminInterface::class));
        }

        if (($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_PROMOTE_INITIALIZE, new GetResponseUserEvent($user, $request))) instanceof Response) {
            return $response;
        }

        if (!$user->isAdmin()) {
            $user->setAdmin(true);
            $this->em->flush();
        }

        if (($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_PROMOTE_SUCCESS, new GetResponseUserEvent($user, $request))) instanceof Response) {
            return $response;
        }

        if ($this->isGranted('PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_LIST')) {
            return $this->redirectToRoute('sfs_user_admin_administrators_list');
        }

        return $this->redirectToRoute('sfs_user_admin_users_list');
    }

    public function usersCountWidget(): Response
    {
        return $this->render('@SfsUser/admin/users/widget-users-count.html.twig', [
            'users' => $this->userManager->getRepository()->count(['admin' => false]),
            'administrators' => $this->userManager->getRepository()->count(['admin' => true]),
            'total' => $this->userManager->getRepository()->count([]),
        ]);
    }

    public function usersPendingConfirmCountWidget(): Response
    {
        return $this->render('@SfsUser/admin/users/widget-pending-confirm-count.html.twig', [
            'count' => $this->userManager->getRepository()->count(['confirmedAt' => null]),
        ]);
    }

    public function userConfirm(string $user): Response
    {
        /** @var ?User $user */
        $user = $this->userManager->findUserBy(['id' => $user]);

        $this->denyAccessUnlessGranted('PERMISSION_SFS_USER_ADMIN_USERS_CONFIRM', $user);

        if (!$user instanceof UserInterface || !$user instanceof ConfirmableInterface) {
            throw new Exception(sprintf('User %s class must implement %s to confirm', get_class($user), ConfirmableInterface::class));
        }

        $user->setConfirmationToken(null);
        $user->setConfirmedAt(new DateTime());
        $this->userManager->saveEntity($user);

        return $this->redirectToRoute('sfs_user_admin_users_details', ['user' => $user->getId()]);
    }

    public function userUnconfirm(string $user): Response
    {
        /** @var ?User $user */
        $user = $this->userManager->findUserBy(['id' => $user]);

        $this->denyAccessUnlessGranted('PERMISSION_SFS_USER_ADMIN_USERS_UNCONFIRM', $user);

        if (!$user instanceof UserInterface || !$user instanceof ConfirmableInterface) {
            throw new Exception(sprintf('User %s class must implement %s to confirm', get_class($user), ConfirmableInterface::class));
        }

        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $user->setConfirmedAt(null);
        $this->userManager->saveEntity($user);

        return $this->redirectToRoute('sfs_user_admin_users_details', ['user' => $user->getId()]);
    }

    public function userEnable(string $user): Response
    {
        /** @var ?User $user */
        $user = $this->userManager->findUserBy(['id' => $user]);

        $this->denyAccessUnlessGranted('PERMISSION_SFS_USER_ADMIN_USERS_ENABLE', $user);

        if (!$user instanceof UserInterface || !$user instanceof EnablableInterface) {
            throw new Exception(sprintf('User %s class must implement %s to enable', get_class($user), EnablableInterface::class));
        }

        $user->setEnabled(true);
        $this->userManager->saveEntity($user);

        return $this->redirectToRoute('sfs_user_admin_users_details', ['user' => $user->getId()]);
    }

    public function userDisable(string $user): Response
    {
        /** @var ?User $user */
        $user = $this->userManager->findUserBy(['id' => $user]);

        $this->denyAccessUnlessGranted('PERMISSION_SFS_USER_ADMIN_USERS_DISABLE', $user);

        if (!$user instanceof UserInterface || !$user instanceof EnablableInterface) {
            throw new Exception(sprintf('User %s class must implement %s to enable', get_class($user), EnablableInterface::class));
        }

        $user->setEnabled(false);
        $this->userManager->saveEntity($user);

        return $this->redirectToRoute('sfs_user_admin_users_details', ['user' => $user->getId()]);
    }

    public function resendConfirmationEmail(string $user, Request $request): Response
    {
        /** @var ConfirmableInterface|UserInterface $user */
        $user = $this->userManager->findUserBy(['id' => $user]);

        if (($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_INITIALIZE, new GetResponseUserEvent($user, $request))) instanceof Response) {
            return $response;
        }

        if (!$user->isConfirmed()) {
            try {
                $this->userMailer && $this->userMailer->sendRegisterConfirmationEmail($user);

                if (($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_SUCCESS, new GetResponseUserEvent($user, $request))) instanceof Response) {
                    return $response;
                }
            } catch (TransportExceptionInterface $e) {
                if (($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_ERROR, new GetResponseUserEvent($user, $request))) instanceof Response) {
                    return $response;
                }
            }
        } elseif (($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_ALREADY_CONFIRMED, new GetResponseUserEvent($user, $request))) instanceof Response) {
            return $response;
        }

        return $this->redirectToRoute('sfs_user_admin_users_details', ['user' => $user]);
    }
}
