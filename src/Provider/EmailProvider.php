<?php

namespace Softspring\UserBundle\Provider;

class EmailProvider extends UserProvider
{
    protected function getUser(string $email)
    {
        return $this->userManager->findUserByEmail($email);
    }
}
