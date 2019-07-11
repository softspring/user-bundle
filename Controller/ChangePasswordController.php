<?php

namespace Softspring\UserBundle\Controller;

use Softspring\ExtraBundle\Controller\AbstractController;
use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\User\Manager\UserManagerInterface;
use Softspring\User\Model\UserInterface;
use Softspring\UserBundle\Form\ChangePasswordFormInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordController extends AbstractController
{
    use Traits\DispatchTrait;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ChangePasswordFormInterface
     */
    protected $changePasswordForm;

    /**
     * ChangePasswordController constructor.
     *
     * @param UserManagerInterface        $userManager
     * @param EventDispatcherInterface    $eventDispatcher
     * @param ChangePasswordFormInterface $changePasswordForm
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, ChangePasswordFormInterface $changePasswordForm)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->changePasswordForm = $changePasswordForm;
    }

    public function changePassword(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $form = $this->createForm(get_class($this->changePasswordForm), $user, [
            'method' => 'POST',
        ])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_PASSWORD_FORM_VALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                $this->userManager->save($user);

                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_PASSWORD_UPDATED, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }

                return $this->redirectToRoute('sfs_user_preferences');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_PASSWORD_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        return $this->render('@SfsUser/change_password/change_password.html.twig', [
            'change_password_form' => $form->createView(),
        ]);
    }
}