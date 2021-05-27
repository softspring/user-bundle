<?php

namespace Softspring\UserBundle\Controller;

use Softspring\CoreBundle\Controller\AbstractController;
use Softspring\CoreBundle\Event\GetResponseFormEvent;
use Softspring\CoreBundle\Event\ViewEvent;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Form\RegisterFormInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var RegisterFormInterface
     */
    protected $registerForm;

    /**
     * @var string|null
     */
    protected $targetPathParameter;

    /**
     * RegisterController constructor.
     *
     * @param UserManagerInterface  $userManager
     * @param RegisterFormInterface $registerForm
     * @param string|null           $targetPathParameter
     */
    public function __construct(UserManagerInterface $userManager, RegisterFormInterface $registerForm, ?string $targetPathParameter)
    {
        $this->userManager = $userManager;
        $this->registerForm = $registerForm;
        $this->targetPathParameter = $targetPathParameter;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function register(Request $request): Response
    {
        $user = $this->userManager->createEntity();

        if ($response = $this->dispatchGetResponse(SfsUserEvents::REGISTER_INITIALIZE, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        $loginCheckParams = [];
        if ($this->targetPathParameter && $targetPath = $request->get($this->targetPathParameter)) {
            $loginCheckParams[$this->targetPathParameter] = $targetPath;
        }

        $form = $this->createForm(get_class($this->registerForm), $user, ['method' => 'POST'])->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($response = $this->dispatchGetResponse(SfsUserEvents::REGISTER_FORM_VALID, new GetResponseFormEvent($form, $request))) {
                    return $response;
                }

                $this->userManager->saveEntity($user);

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

        $viewData = new \ArrayObject([
            'register_form' => $form->createView(),
            'register_params' => $loginCheckParams,
        ]);

        $this->dispatch(SfsUserEvents::REGISTER_VIEW, new ViewEvent($viewData));

        return $this->render('@SfsUser/register/register.html.twig', $viewData->getArrayCopy());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function success(Request $request): Response
    {
        $viewData = new \ArrayObject([]);

        $this->dispatch(SfsUserEvents::REGISTER_SUCCESS_VIEW, new ViewEvent($viewData));

        return $this->render('@SfsUser/register/success.html.twig', $viewData->getArrayCopy());
    }

    /**
     * @param string  $user
     * @param string  $token
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function confirm(string $user, string $token, Request $request): Response
    {
        /** @var UserInterface|ConfirmableInterface $user */
        $user = $this->userManager->getRepository()->findOneById($user);

        if ($user->getConfirmationToken() !== $token) {
            if ($response = $this->dispatchGetResponse(SfsUserEvents::CONFIRMATION_FAILED, new GetResponseUserEvent($user, $request))) {
                return $response;
            }

            return $this->redirectToRoute('sfs_user_register');
        }

        if ($response = $this->dispatchGetResponse(SfsUserEvents::CONFIRMATION_VALID, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        $user->setConfirmationToken(null);
        $user->setConfirmedAt(new \DateTime('now'));
        $this->userManager->saveEntity($user);

        if ($response = $this->dispatchGetResponse(SfsUserEvents::CONFIRMATION_SUCCESS, new GetResponseUserEvent($user, $request))) {
            return $response;
        }

        $viewData = new \ArrayObject([]);

        $this->dispatch(SfsUserEvents::CONFIRMATION_VIEW, new ViewEvent($viewData));

        return $this->render('@SfsUser/register/confirmed.html.twig', $viewData->getArrayCopy());
    }
}