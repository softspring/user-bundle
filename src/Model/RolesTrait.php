<?php

namespace Softspring\UserBundle\Model;

trait RolesTrait
{
    protected array $roles = [];

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
