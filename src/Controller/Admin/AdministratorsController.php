<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Manager\AdminUserManagerInterface;
use Softspring\UserBundle\Model\RolesAdminInterface;
use Softspring\UserBundle\Model\User;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministratorsController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected AdminUserManagerInterface $userManager;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(AdminUserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function demoteAdmin(string $administrator, Request $request): Response
    {
        /** @var User|RolesAdminInterface $administrator */
        $administrator = $this->userManager->findUserBy(['id' => $administrator]);

        $this->denyAccessUnlessGranted('PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_DEMOTE', $administrator);

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_ADMINISTRATORS_DEMOTE_INITIALIZE, new GetResponseUserEvent($administrator, $request))) {
            return $response;
        }

        if ($administrator->isAdmin()) {
            $administrator->setAdmin(false);
            $this->userManager->getEntityManager()->flush();
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_ADMINISTRATORS_DEMOTE_SUCCESS, new GetResponseUserEvent($administrator, $request))) {
            return $response;
        }

        if ($this->isGranted('PERMISSION_SFS_USER_ADMIN_USERS_LIST')) {
            return $this->redirectToRoute('sfs_user_admin_users_list');
        } else {
            return $this->redirectToRoute('sfs_user_admin_administrators_list');
        }
    }

    public function promoteSuper(string $administrator, Request $request): Response
    {
        /** @var User|RolesAdminInterface $administrator */
        $administrator = $this->userManager->findUserBy(['id' => $administrator]);

        $this->denyAccessUnlessGranted('PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_PROMOTE_SUPER', $administrator);

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_ADMINISTRATORS_PROMOTE_SUPER_INITIALIZE, new GetResponseUserEvent($administrator, $request))) {
            return $response;
        }

        if ($administrator->isAdmin()) {
            $administrator->setSuperAdmin(true);
            $this->userManager->getEntityManager()->flush();
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_ADMINISTRATORS_PROMOTE_SUPER_SUCCESS, new GetResponseUserEvent($administrator, $request))) {
            return $response;
        }

        if ($this->isGranted('PERMISSION_SFS_USER_ADMIN_USERS_LIST')) {
            return $this->redirectToRoute('sfs_user_admin_users_list');
        } else {
            return $this->redirectToRoute('sfs_user_admin_administrators_list');
        }
    }

    public function demoteSuper(string $administrator, Request $request): Response
    {
        /** @var User|RolesAdminInterface $administrator */
        $administrator = $this->userManager->findUserBy(['id' => $administrator]);

        $this->denyAccessUnlessGranted('PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_DEMOTE_SUPER', $administrator);

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_ADMINISTRATORS_DEMOTE_SUPER_INITIALIZE, new GetResponseUserEvent($administrator, $request))) {
            return $response;
        }

        if ($administrator->isAdmin()) {
            $administrator->setSuperAdmin(false);
            $this->userManager->getEntityManager()->flush();
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_ADMINISTRATORS_DEMOTE_SUPER_SUCCESS, new GetResponseUserEvent($administrator, $request))) {
            return $response;
        }

        if ($this->isGranted('PERMISSION_SFS_USER_ADMIN_USERS_LIST')) {
            return $this->redirectToRoute('sfs_user_admin_users_list');
        } else {
            return $this->redirectToRoute('sfs_user_admin_administrators_list');
        }
    }
}
