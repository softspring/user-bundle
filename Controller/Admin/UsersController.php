<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\Account\Model\UserMultiAccountedInterface;
use Softspring\ExtraBundle\Controller\AbstractController;
use Softspring\User\Manager\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected $impersonateBarConfig;

    /**
     * UsersController constructor.
     * @param UserManagerInterface $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $impersonateBarConfig
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, array $impersonateBarConfig)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->impersonateBarConfig = $impersonateBarConfig;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function list(Request $request): Response
    {
        $repo = $this->userManager->getRepository();

        $users = $repo->findBy(['admin'=>false]);

        return $this->render('@SfsUser/admin/users/list.html.twig', [
            'users' => $users,
            'switch_enabled' => $this->impersonateBarConfig['enabled'] ?? false,
            'switch_role' => $this->impersonateBarConfig['switch_role'] ?? null,
            'switch_route' => $this->impersonateBarConfig['switch_route'] ?? null,
            'switch_route_params' => $this->impersonateBarConfig['switch_route_params'] ?? null,
            'switch_parameter' => $this->impersonateBarConfig['switch_parameter'] ?? null,
        ]);
    }

    /**
     * @param string  $user
     * @param Request $request
     *
     * @return Response
     */
    public function details(string $user, Request $request): Response
    {
        $user = $this->userManager->findUserBy(['id' => $user]);

        if ($user->isAdmin()) {
            return $this->redirectToRoute('sfs_user_admin_users_list');
        }

        return $this->render('@SfsUser/admin/users/details.html.twig', [
            'user' => $user,
            'multi_accounted_user' => $user instanceof UserMultiAccountedInterface,
        ]);
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

        if (!$user->isAdmin()) {
            $user->setAdmin(true);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('sfs_user_admin_administrators_list');
    }
}