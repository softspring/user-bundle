<?php

namespace Softspring\UserBundle\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use Softspring\CoreBundle\Controller\Traits\DispatchGetResponseTrait;
use Softspring\CoreBundle\Event\GetResponseEvent;
use Softspring\CoreBundle\Event\GetResponseFormEvent;
use Softspring\CoreBundle\Event\ViewEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Form\ResetPasswordFormInterface;
use Softspring\UserBundle\Form\ResetPasswordRequestFormInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\PasswordRequestInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends AbstractController
{
    use DispatchGetResponseTrait;

    protected UserManagerInterface $userManager;

    protected ResetPasswordRequestFormInterface $resetRequestForm;

    protected int $resetTokenTTL;

    protected ResetPasswordFormInterface $resetForm;

    protected EventDispatcherInterface   $eventDispatcher;

    public function __construct(UserManagerInterface $userManager, ResetPasswordRequestFormInterface $resetRequestForm, int $resetTokenTTL, ResetPasswordFormInterface $resetForm, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->resetRequestForm = $resetRequestForm;
        $this->resetTokenTTL = $resetTokenTTL;
        $this->resetForm = $resetForm;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function request(Request $request): Response
    {
        if ($response = $this->dispatchGetResponse(SfsUserEvents::RESET_REQUEST_INITIALIZE, new GetResponseEvent())) {
            return $response;
        }

        $form = $this->createForm(get_class($this->resetRequestForm), [], ['method' => 'POST'])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::RESET_REQUEST_FORM_VALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                $request->getSession()->set('requested_email', $form->get('email')->getData());

                // REQUEST PERFORM (SEND EMAIL, ETC) IS DONE ON EVENT LISTENERS
                if ($response = $this->dispatchGetResponse(SfsUserEvents::RESET_REQUEST_DO_REQUEST, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                return $this->redirectToRoute('sfs_user_reset_password_requested');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::RESET_REQUEST_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        $viewData = new \ArrayObject([
            'reset_form' => $form->createView(),
        ]);

        $this->dispatch(SfsUserEvents::RESET_REQUEST_VIEW, new ViewEvent($viewData));

        return $this->render('@SfsUser/reset_password/request.html.twig', $viewData->getArrayCopy());
    }

    public function requested(Request $request): Response
    {
        $requestedEmail = $request->getSession()->get('requested_email');

        if (!$requestedEmail) {
            return $this->redirectToRoute('sfs_user_reset_password_request');
        }

        $this->dispatch(SfsUserEvents::RESET_REQUESTED_VIEW, new ViewEvent($viewData = new \ArrayObject([
            'resetTokenTTL' => $this->resetTokenTTL,
            'requestedEmail' => $requestedEmail,
        ])));

        return $this->render('@SfsUser/reset_password/requested.html.twig', $viewData->getArrayCopy());
    }

    public function reset(string $user, string $token, Request $request): Response
    {
        if ($response = $this->dispatchGetResponse(SfsUserEvents::RESET_INITIALIZE, new GetResponseEvent())) {
            return $response;
        }

        /** @var UserInterface|PasswordRequestInterface $user */
        $user = $this->userManager->getRepository()->findOneById($user);

        if ($user->getPasswordRequestToken() !== $token) {
            return $this->redirectToRoute('sfs_user_reset_password_request');
        }

//        // TODO check if expired
//        if (($user->getPasswordRequestedAt()->format('u') - (new \DateTime('now'))->format('u')) < $this->resetTokenTTL) {
//            // TODO fuck this
//        }

        $form = $this->createForm(get_class($this->resetForm), $user, ['method' => 'POST'])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::RESET_FORM_VALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                $user->setPasswordRequestedAt(null);
                $user->setPasswordRequestToken(null);
                $this->userManager->saveEntity($user);

                if ($response = $this->dispatchGetResponse(SfsUserEvents::RESET_SUCCESS, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }

                return $this->redirectToRoute('sfs_user_reset_password_success');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::RESET_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        $viewData = new \ArrayObject([
            'reset_form' => $form->createView(),
        ]);

        $this->dispatch(SfsUserEvents::RESET_VIEW, new ViewEvent($viewData));

        return $this->render('@SfsUser/reset_password/reset.html.twig', $viewData->getArrayCopy());
    }

    public function success(Request $request): Response
    {
        $viewData = new \ArrayObject([]);

        $this->dispatch(SfsUserEvents::RESET_SUCCESS_VIEW, new ViewEvent($viewData));

        return $this->render('@SfsUser/reset_password/success.html.twig', $viewData->getArrayCopy());
    }
}
