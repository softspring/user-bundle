<?php

namespace Softspring\UserBundle\Controller\Admin;

use Softspring\Account\Model\UserMultiAccountedInterface;
use Softspring\AdminBundle\Event\ViewEvent;
use Softspring\ExtraBundle\Controller\AbstractController;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Form\Admin\UserDeleteFormInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
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
     * @var UserDeleteFormInterface
     */
    protected $deleteForm;

    /**
     * UsersController constructor.
     * @param UserManagerInterface $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $impersonateBarConfig
     * @param UserDeleteFormInterface $deleteForm
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, array $impersonateBarConfig, UserDeleteFormInterface $deleteForm)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->impersonateBarConfig = $impersonateBarConfig;
        $this->deleteForm = $deleteForm;
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

    /**
     * @param string  $user
     * @param Request $request
     *
     * @return Response
     */
    public function delete(string $user, Request $request): Response
    {
        $user = $this->userManager->findUserBy(['id' => $user]);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_DELETE_INITIALIZE, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        $form = $this->getDeleteForm($user)->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_DELETE_FORM_VALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                $this->userManager->delete($user);

                if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_DELETE_SUCCESS, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }

                return $this->redirectToRoute('sfs_user_admin_users_list');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::ADMIN_USERS_DELETE_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        // show view
        $viewData = new \ArrayObject([
            'form' => $form->createView(),
            'user' => $user,
        ]);

        $this->eventDispatcher->dispatch(new ViewEvent($viewData), SfsUserEvents::ADMIN_USERS_DELETE_VIEW);

        return $this->render('@SfsUser/admin/users/delete.html.twig', $viewData->getArrayCopy());
    }

    protected function getDeleteForm(UserInterface $user): FormInterface
    {
        return $this->createForm(get_class($this->deleteForm), $user, [
            'method' => 'POST',
            'user' => $user,
        ]);
    }
}