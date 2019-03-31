<?php

namespace Softspring\UserBundle\Controller;

use Softspring\UserBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Form\RegisterFormInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends AbstractController
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
     * @var RegisterFormInterface
     */
    protected $registerForm;

    /**
     * RegisterController constructor.
     *
     * @param UserManagerInterface     $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param RegisterFormInterface    $registerForm
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher, RegisterFormInterface $registerForm)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->registerForm = $registerForm;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function register(Request $request): Response
    {
        $user = $this->userManager->create();

        if ($response = $this->dispatchGetResponse(SfsUserEvents::REGISTER_INITIALIZE, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        $form = $this->createForm(get_class($this->registerForm), $user, ['method' => 'POST'])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::REGISTER_FORM_VALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                $this->userManager->save($user);

                if ($response = $this->dispatchGetResponse(SfsUserEvents::REGISTER_SUCCESS, new GetResponseUserEvent($user, $request))) {
                    return $response;
                }

                return $this->redirectToRoute('sfs_user_register_success');
            } else {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::REGISTER_FORM_INVALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }
            }
        }

        return $this->render('@SfsUser/register/register.html.twig', [
            'register_form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function success(Request $request): Response
    {
        return $this->render('@SfsUser/register/success.html.twig', [

        ]);
    }
}