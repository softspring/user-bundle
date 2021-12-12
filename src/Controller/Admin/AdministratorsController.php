<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministratorsController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * AdministratorsController constructor.
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param string  $administrator
     * @param Request $request
     *
     * @return Response
     */
    public function demoteAdmin(string $administrator, Request $request): Response
    {
        $administrator = $this->userManager->findUserBy(['id' => $administrator]);

        $this->denyAccessUnlessGranted('ROLE_ADMIN_ADMINISTRATORS_DEMOTE', $administrator);

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_ADMINISTRATORS_DEMOTE_INITIALIZE, new GetResponseUserEvent($administrator, $request))) {
            return $response;
        }

        if ($administrator->isAdmin()) {
            $administrator->setAdmin(false);
            $this->getDoctrine()->getManager()->flush();
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