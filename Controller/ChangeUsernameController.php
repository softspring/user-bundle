<?php

namespace Softspring\UserBundle\Controller;

use Softspring\User\Event\GetResponseFormEvent;
use Softspring\User\Event\GetResponseUserEvent;
use Softspring\User\Manager\UserManagerInterface;
use Softspring\User\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangeUsernameController extends AbstractController
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
    protected $changeUsernameConfig;

    /**
     * ChangeUsernameController constructor.
     *
     * @param UserManagerInterface     $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param array                    $changeUsernameConfig
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, array $changeUsernameConfig)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->changeUsernameConfig = $changeUsernameConfig;
    }

    public function changeUsername(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $form = $this->createForm($this->getParameter('sfs_user.change_username.form'), $user, [
            'method' => 'POST',
        ])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_USERNAME_FORM_VALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                $this->userManager->save($user);

                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_USERNAME_UPDATED, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }

                return $this->redirect('/');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_USERNAME_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        return $this->render($this->getParameter('sfs_user.change_username.template'), [
            'change_username_form' => $form->createView(),
        ]);
    }
}