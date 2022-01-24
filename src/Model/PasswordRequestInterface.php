<?php

namespace Softspring\UserBundle\Model;

interface PasswordRequestInterface
{
    public function getPasswordRequestedAt(): ?\DateTime;

    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): void;

    public function getPasswordRequestToken(): ?string;

    public function setPasswordRequestToken(?string $passwordRequestToken): void;
}
