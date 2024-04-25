<?php

namespace Softspring\UserBundle\Model;

use DateTime;

interface UserLastLoginInterface
{
    public function getLastLogin(): ?DateTime;

    public function setLastLogin(?DateTime $lastLogin): void;
}
