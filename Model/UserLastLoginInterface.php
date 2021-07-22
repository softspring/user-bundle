<?php

namespace Softspring\UserBundle\Model;

interface UserLastLoginInterface
{
    /**
     * @return \DateTime|null
     */
    public function getLastLogin(): ?\DateTime;

    /**
     * @param \DateTime|null $lastLogin
     */
    public function setLastLogin(?\DateTime $lastLogin): void;
}