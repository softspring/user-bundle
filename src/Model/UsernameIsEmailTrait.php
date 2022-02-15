<?php

namespace Softspring\UserBundle\Model;

trait UsernameIsEmailTrait
{
    public function setEmail(?string $email): void
    {
        $this->email = $email;
        $this->username = $email;
    }
}
