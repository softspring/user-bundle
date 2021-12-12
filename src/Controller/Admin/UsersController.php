<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class UsersController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var UserMailerInterface
     */
    protected $userMailer;

    /**
     * UsersController constructor.
     *
     * @param UserManagerInterface $userManager
     * @param UserMailerInterface  $userMailer
     */
    public function __construct(UserManagerInterface $userManager, UserMailerInterface $userMailer)
    {
        $this->userManager = $userManager;
        $this->userMailer = $userMailer;
    }

    /**
     * @param string  $user
     * @param Request $request
     *
     * @return Response
     */
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
            'users' => $this->userManager->getRepository()->count(['admin'=>0]),
            'administrators' => $this->userManager->getRepository()->count(['admin'=>1]),
            'total' => $this->userManager->getRepository()->count([]),
        ]);
    }

    public function resendEmail(string $user, Request $request): Response
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