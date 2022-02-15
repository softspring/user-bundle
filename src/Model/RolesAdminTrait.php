<?php

namespace Softspring\UserBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

trait RolesAdminTrait
{
    protected bool $admin = false;

    protected bool $superAdmin = false;

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;

        if (!$admin) {
            $this->setSuperAdmin(false);
        }
    }

    public function isSuperAdmin(): bool
    {
        return $this->superAdmin;
    }

    public function setSuperAdmin(bool $superAdmin): void
    {
        $this->superAdmin = $superAdmin;

        if ($superAdmin) {
            $this->setAdmin(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = [];

        // we need to make sure to have at least one role
        $roles[] = 'ROLE_USER';

        if ($this->isAdmin()) {
            $roles[] = 'ROLE_ADMIN';
        }

        if ($this->isSuperAdmin()) {
            $roles[] = 'ROLE_SUPER_ADMIN';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $rolesCollection = new ArrayCollection($roles);

        if ($rolesCollection->contains('ROLE_ADMIN')) {
            $rolesCollection->removeElement('ROLE_ADMIN');
            $this->setAdmin(true);
        }

        if ($rolesCollection->contains('ROLE_SUPER_ADMIN')) {
            $rolesCollection->removeElement('ROLE_SUPER_ADMIN');
            $this->setSuperAdmin(true);
        }
    }
}
