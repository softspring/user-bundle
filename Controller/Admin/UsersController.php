<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\UserBundle\Controller\Traits\DispatchTrait;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends AbstractController
{
    use DispatchTrait;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * UsersController constructor.
     *
     * @param UserManagerInterface     $userManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
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