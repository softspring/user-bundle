<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * UsersController constructor.
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
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
}