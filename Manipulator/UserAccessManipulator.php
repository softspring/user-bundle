<?php


namespace Softspring\UserBundle\Manipulator;


use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Model\UserAccessInterface;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class UserAccessManipulator
{
    /**
     * @var UserAccessManagerInterface
     */
    protected $userAccessManager;

    /**
     * UserAccessManipulator constructor.
     *
     * @param UserAccessManagerInterface $userAccessManager
     */
    public function __construct(UserAccessManagerInterface $userAccessManager)
    {
        $this->userAccessManager = $userAccessManager;
    }

    /**
     * @inheritdoc
     */
    public function register(UserInterface $user, Request $request): UserAccessInterface
    {
        $userAccess = $this->userAccessManager->create();
        $userAccess->setUser($user);
        $userAccess->setLoginAt(new \DateTime('now'));
        $userAccess->setIp($request->getClientIp());
        $userAccess->setUserAgent($request->headers->get('User-Agent'));

        $this->userAccessManager->save($userAccess);

        return $userAccess;
    }
}