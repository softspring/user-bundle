<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\UserBundle\Manager\UserManagerInterface;
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