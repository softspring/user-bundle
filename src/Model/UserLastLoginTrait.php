<?php

namespace Softspring\UserBundle\Model;

trait UserLastLoginTrait
{
    protected ?int $lastLogin = null;

    public function getLastLogin(): ?\DateTime
    {
        return \DateTime::createFromFormat('U', $this->lastLogin) ?: null;
    }

    public function setLastLogin(?\DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin instanceof \DateTime ? $lastLogin->format('U') : null;
    }
}
