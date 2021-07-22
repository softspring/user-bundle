<?php

namespace Softspring\UserBundle\Model\Traits;

trait UserLastLoginTrait
{
    /**
     * @var int|null
     */
    protected $lastLogin;

    /**
     * @return \DateTime|null
     */
    public function getLastLogin(): ?\DateTime
    {
        return \DateTime::createFromFormat("U", $this->lastLogin) ?: null;
    }

    /**
     * @param \DateTime|null $lastLogin
     */
    public function setLastLogin(?\DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin instanceof \DateTime ? $lastLogin->format('U') : null;
    }
}