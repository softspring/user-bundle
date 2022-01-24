<?php

namespace Softspring\UserBundle\Model;

interface UserLastLoginInterface
{
    public function getLastLogin(): ?\DateTime;

    public function setLastLogin(?\DateTime $lastLogin): void;
}
