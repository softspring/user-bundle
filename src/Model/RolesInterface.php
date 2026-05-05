<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Model;

interface RolesInterface
{
    public function setRoles(array $roles): void;

    public function getRoles(): array;
}
