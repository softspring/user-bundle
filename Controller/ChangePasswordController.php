<?php

namespace Softspring\UserBundle\Controller;

use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @var array
     */
    protected $changePasswordConfig;

    /**
     * ChangePasswordController constructor.
     *
     * @param UserManagerInterface     $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param array                    $changePasswordConfig
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, array $changePasswordConfig)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->changePasswordConfig = $changePasswordConfig;
    }

    public function changePassword(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $form = $this->createForm($this->getParameter('sfs_user.change_password.form'), $user, [
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

                return $this->redirect('/');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_PASSWORD_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        return $this->render($this->getParameter('sfs_user.change_password.template'), [
            'change_password_form' => $form->createView(),
        ]);
    }
}