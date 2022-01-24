<?php

namespace Softspring\UserBundle\Entity;

trait UsernameIsEmailTrait
{
    public function setEmail(?string $email): void
    {
        $this->email = $email;
        $this->username = $email;
    }
}
