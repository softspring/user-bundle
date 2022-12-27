<?php

namespace Softspring\UserBundle\Model;

trait UserIdentifierUsernameTrait
{
    protected ?string $username = null;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }
}
