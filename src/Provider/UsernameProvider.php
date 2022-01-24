<?php

namespace Softspring\UserBundle\Provider;

class UsernameProvider extends UserProvider
{
    protected function getUser(string $username)
    {
        return $this->userManager->findUserByUsername($username);
    }
}
