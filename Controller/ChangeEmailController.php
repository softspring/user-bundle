<?php

namespace Softspring\UserBundle\Controller;

use Softspring\ExtraBundle\Controller\AbstractController;
use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangeEmailController extends AbstractController
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
    protected $changeEmailConfig;

    /**
     * ChangeEmailController constructor.
     *
     * @param UserManagerInterface     $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param array                    $changeEmailConfig
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, array $changeEmailConfig)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->changeEmailConfig = $changeEmailConfig;
    }

    public function changeEmail(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $form = $this->createForm($this->getParameter('sfs_user.change_email.form'), $user, [
            'method' => 'POST',
        ])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_EMAIL_FORM_VALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                $this->userManager->save($user);

                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_EMAIL_UPDATED, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }

                return $this->redirect('/');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::CHANGE_EMAIL_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        return $this->render($this->getParameter('sfs_user.change_email.template'), [
            'change_email_form' => $form->createView(),
        ]);
    }
}