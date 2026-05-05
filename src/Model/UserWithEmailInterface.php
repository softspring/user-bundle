<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Model;

interface UserWithEmailInterface
{
    public function getEmail(): ?string;

    public function setEmail(?string $email): void;
}
