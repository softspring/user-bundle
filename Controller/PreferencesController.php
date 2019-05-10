<?php

namespace Softspring\UserBundle\Controller;

use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\User\Manager\UserManagerInterface;
use Softspring\User\Model\UserInterface;
use Softspring\UserBundle\Form\PreferencesFormInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PreferencesController extends AbstractController
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
     * @var PreferencesFormInterface
     */
    protected $preferencesForm;

    /**
     * PreferencesController constructor.
     * @param UserManagerInterface $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param PreferencesFormInterface $preferencesForm
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, PreferencesFormInterface $preferencesForm)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->preferencesForm = $preferencesForm;
    }

    public function preferences(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $form = $this->createForm(get_class($this->preferencesForm), $user, [
            'method' => 'POST',
        ])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::PREFERENCES_FORM_VALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                $this->userManager->save($user);

                if ($response = $this->dispatchGetResponse(SfsUserEvents::PREFERENCES_UPDATED, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }

                return $this->redirectToRoute('sfs_user_preferences');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::PREFERENCES_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        return $this->render('@SfsUser/preferences/preferences.html.twig', [
            'preferences_form' => $form->createView(),
        ]);
    }
}