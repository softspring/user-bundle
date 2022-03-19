<?php

namespace Softspring\UserBundle\Provider;

use Softspring\UserBundle\Model\UserIdentifierEmailInterface;

class EmailProvider extends UserProvider
{
    protected function getUser(string $email)
    {
        if (!$this->userManager->getEntityClassReflection()->implementsInterface(UserIdentifierEmailInterface::class)) {
            throw new \Exception(sprintf('User must be an instance of %s interface to use %s', UserIdentifierEmailInterface::class, self::class));
        }

        return $this->userManager->findUserByEmail($email);
    }
}
