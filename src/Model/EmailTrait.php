<?php

namespace Softspring\UserBundle\Model;

trait EmailTrait
{
    protected ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
