<?php

namespace Softspring\UserBundle\Controller;

use Softspring\ExtraBundle\Controller\AbstractController;
use Softspring\ExtraBundle\Event\GetResponseEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\ViewEvent;
use Softspring\UserBundle\Form\ResetPasswordRequestFormInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends AbstractController
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
     * @var ResetPasswordRequestFormInterface
     */
    protected $resetRequestForm;

    /**
     * @var int
     */
    protected $resetTokenTTL;

    /**
     * ResetPasswordController constructor.
     * @param UserManagerInterface $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param ResetPasswordRequestFormInterface $resetRequestForm
     * @param int $resetTokenTTL
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, ResetPasswordRequestFormInterface $resetRequestForm, int $resetTokenTTL)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->resetRequestForm = $resetRequestForm;
        $this->resetTokenTTL = $resetTokenTTL;
    }

    /**
     * @param Request $request
     * @return Response
     */
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

         $this->eventDispatcher->dispatch(new ViewEvent($viewData), SfsUserEvents::RESET_REQUEST_VIEW);

        return $this->render('@SfsUser/reset_password/request.html.twig', $viewData->getArrayCopy());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function requested(Request $request): Response
    {
        $requestedEmail = $request->getSession()->get('requested_email');

        if (!$requestedEmail) {
            return $this->redirectToRoute('sfs_user_reset_password_request');
        }

        $this->eventDispatcher->dispatch(new ViewEvent($viewData = new \ArrayObject([
            'resetTokenTTL' => $this->resetTokenTTL,
            'requestedEmail' => $requestedEmail,
        ])), SfsUserEvents::RESET_REQUESTED_VIEW);

        return $this->render('@SfsUser/reset_password/requested.html.twig', $viewData->getArrayCopy());
    }

    public function reset(Request $request): Response
    {

    }

    public function success(Request $request): Response
    {

    }
}