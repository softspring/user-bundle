<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Model;

use DateTime;

interface PasswordRequestInterface
{
    public function getPasswordRequestedAt(): ?DateTime;

    public function setPasswordRequestedAt(?DateTime $passwordRequestedAt): void;

    public function getPasswordRequestToken(): ?string;

    public function setPasswordRequestToken(?string $passwordRequestToken): void;
}
