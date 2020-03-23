<?php

namespace Softspring\UserBundle\Model;

interface UserWithEmailInterface
{
    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void;
}