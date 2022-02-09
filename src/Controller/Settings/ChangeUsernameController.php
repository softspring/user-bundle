<?php

namespace Softspring\UserBundle\Controller\Settings;

use Softspring\CoreBundle\Controller\Traits\DispatchGetResponseTrait;
use Softspring\CoreBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Form\Settings\ChangeUsernameFormInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangeUsernameController extends AbstractController
{
    use DispatchGetResponseTrait;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var ChangeUsernameFormInterface
     */
    protected $changeUsernameForm;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param UserManagerInterface        $userManager
     * @param ChangeUsernameFormInterface $changeUsernameForm
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(UserManagerInterface $userManager, ChangeUsernameFormInterface $changeUsernameForm, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->changeUsernameForm = $changeUsernameForm;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function changeUsername(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $form = $this->createForm(get_class($this->changeUsernameForm), $user, [
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

        return $this->render('@SfsUser/change_username/change_username.html.twig', [
            'change_username_form' => $form->createView(),
        ]);
    }
}
