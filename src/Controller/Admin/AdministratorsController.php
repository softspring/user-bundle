<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\CoreBundle\Controller\Traits\DispatchGetResponseTrait;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
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

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param UserManagerInterface     $userManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function demoteAdmin(string $administrator, Request $request): Response
    {
        /** @var User|RolesAdminInterface $administrator */
        $administrator = $this->userManager->findUserBy(['id' => $administrator]);

        $this->denyAccessUnlessGranted('ROLE_ADMIN_ADMINISTRATORS_DEMOTE', $administrator);

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

        if ($this->isGranted('ROLE_ADMIN_USERS_LIST')) {
            return $this->redirectToRoute('sfs_user_admin_users_list');
        } else {
            return $this->redirectToRoute('sfs_user_admin_administrators_list');
        }
    }
}
