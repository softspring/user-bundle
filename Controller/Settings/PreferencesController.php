<?php

namespace Softspring\UserBundle\Controller\Settings;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\CoreBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Form\PreferencesFormInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PreferencesController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var PreferencesFormInterface
     */
    protected $preferencesForm;

    /**
     * PreferencesController constructor.
     *
     * @param UserManagerInterface     $userManager
     * @param PreferencesFormInterface $preferencesForm
     */
    public function __construct(UserManagerInterface $userManager, PreferencesFormInterface $preferencesForm)
    {
        $this->userManager = $userManager;
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