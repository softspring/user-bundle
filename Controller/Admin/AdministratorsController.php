<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\ExtraBundle\Controller\AbstractController;
use Softspring\UserBundle\Controller\Traits\DispatchTrait;
use Softspring\User\Manager\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministratorsController extends AbstractController
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

        $administrators = $repo->findBy(['admin' => true]);

        return $this->render('@SfsUser/admin/administrators/list.html.twig', [
            'administrators' => $administrators,
        ]);
    }

    /**
     * @param string  $administrator
     * @param Request $request
     *
     * @return Response
     */
    public function details(string $administrator, Request $request): Response
    {
        $administrator = $this->userManager->findUserBy(['id' => $administrator]);

        if (!$administrator->isAdmin()) {
            return $this->redirectToRoute('sfs_user_admin_administrators_list');
        }

        return $this->render('@SfsUser/admin/administrators/details.html.twig', [
            'administrator' => $administrator,
        ]);
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

        if ($administrator->isAdmin()) {
            $administrator->setAdmin(false);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('sfs_user_admin_users_list');
    }
}