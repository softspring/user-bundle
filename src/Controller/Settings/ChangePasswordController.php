<?php

namespace Softspring\UserBundle\Controller\Settings;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\CoreBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Form\Settings\ChangePasswordFormInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var ChangePasswordFormInterface
     */
    protected $changePasswordForm;

    /**
     * ChangePasswordController constructor.
     */
    public function __construct(UserManagerInterface $userManager, ChangePasswordFormInterface $changePasswordForm)
    {
        $this->userManager = $userManager;
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

                $this->userManager->saveEntity($user);

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
