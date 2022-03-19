<?php

namespace Softspring\UserBundle\Provider;

use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;

class UsernameProvider extends UserProvider
{
    protected function getUser(string $username)
    {
        if (!$this->userManager->getEntityClassReflection()->implementsInterface(UserIdentifierUsernameInterface::class)) {
            throw new \Exception(sprintf('User must be an instance of %s interface to use %s', UserIdentifierUsernameInterface::class, self::class));
        }

        return $this->userManager->findUserByUsername($username);
    }
}
