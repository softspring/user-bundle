<?php

namespace Softspring\UserBundle\Model\Traits;

trait RolesTrait
{
    /**
     * @var array
     */
    protected $roles = [];

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
