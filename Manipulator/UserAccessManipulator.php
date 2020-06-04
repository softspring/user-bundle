<?php

namespace Softspring\UserBundle\Manipulator;

use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Model\UserAccessInterface;
use Softspring\UserBundle\Model\UserAccessLatLongInterface;
use Softspring\UserBundle\Model\UserAccessLocationInterface;
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
        $userAccess = $this->userAccessManager->createEntity();
        $userAccess->setUser($user);
        $userAccess->setLoginAt(new \DateTime('now'));
        $userAccess->setIp($request->getClientIp());
        $userAccess->setUserAgent($request->headers->get('User-Agent'));

        if ($userAccess instanceof UserAccessLocationInterface) {
            if ($request->server->has('GAE_APPLICATION')) {
                // HTTP_X_APPENGINE_CITY
                // HTTP_X_APPENGINE_COUNTRY
                $userAccess->setCity($request->server->get('HTTP_X_APPENGINE_CITY'));
                $userAccess->setRegion($request->server->get('HTTP_X_APPENGINE_REGION'));
                $userAccess->setCountry($request->server->get('HTTP_X_APPENGINE_COUNTRY'));
            }
        }

        if ($userAccess instanceof UserAccessLatLongInterface) {
            if ($request->server->has('GAE_APPLICATION')) {
                // HTTP_X_APPENGINE_CITYLATLONG "37.386051,-122.083851"
                [$lat, $long] = explode(',', $request->server->get('HTTP_X_APPENGINE_CITYLATLONG'));
                $userAccess->setLat($lat);
                $userAccess->setLong($long);
            }
        }

        $this->userAccessManager->saveEntity($userAccess);

        return $userAccess;
    }
}