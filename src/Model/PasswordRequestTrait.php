<?php

namespace Softspring\UserBundle\Model;

trait PasswordRequestTrait
{
    protected ?string $passwordRequestToken = null;

    protected ?int $passwordRequestedAt = null;

    public function getPasswordRequestedAt(): ?\DateTime
    {
        return \DateTime::createFromFormat('U', $this->passwordRequestedAt) ?: null;
    }

    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt instanceof \DateTime ? $passwordRequestedAt->format('U') : null;
    }

    public function getPasswordRequestToken(): ?string
    {
        return $this->passwordRequestToken;
    }

    public function setPasswordRequestToken(?string $passwordRequestToken): void
    {
        $this->passwordRequestToken = $passwordRequestToken;
    }
}
