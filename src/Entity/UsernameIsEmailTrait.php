<?php

namespace Softspring\UserBundle\Entity;

trait UsernameIsEmailTrait
{
    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
        $this->username = $email;
    }
}