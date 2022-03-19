<?php

namespace Softspring\UserBundle\Model;

trait UserIdentifierUsernameTrait
{
    protected ?string $username = null;

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->getUsername();
    }
}