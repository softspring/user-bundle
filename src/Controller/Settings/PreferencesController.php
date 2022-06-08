<?php

namespace Softspring\UserBundle\Controller\Settings;

use Softspring\Component\Events\DispatchGetResponseTrait;
use Softspring\Component\Events\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Form\Settings\PreferencesFormInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PreferencesController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected UserManagerInterface $userManager;

    protected PreferencesFormInterface $preferencesForm;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(UserManagerInterface $userManager, PreferencesFormInterface $preferencesForm, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->preferencesForm = $preferencesForm;
        $this->eventDispatcher = $eventDispatcher;
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

                $this->userManager->saveEntity($user);

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
