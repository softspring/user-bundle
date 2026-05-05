<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Model;

use DateTime;

interface UserLastLoginInterface
{
    public function getLastLogin(): ?DateTime;

    public function setLastLogin(?DateTime $lastLogin): void;
}
