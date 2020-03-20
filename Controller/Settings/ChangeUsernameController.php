<?php

namespace Softspring\UserBundle\Controller\Settings;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\CoreBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangeUsernameController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var array
     */
    protected $changeUsernameConfig;

    /**
     * ChangeUsernameController constructor.
     *
     * @param UserManagerInterface $userManager
     * @param array                $changeUsernameConfig
     */
    public function __construct(UserManagerInterface $userManager, array $changeUsernameConfig)
    {
        $this->userManager = $userManager;
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

                $this->userManager->saveEntity($user);

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