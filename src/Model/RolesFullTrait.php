<?php

namespace Softspring\UserBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

trait RolesFullTrait
{
    use RolesAdminTrait, RolesTrait {
        RolesTrait::getRoles insteadof RolesAdminTrait;
        RolesTrait::setRoles insteadof RolesAdminTrait;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

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

        $this->roles = $rolesCollection->toArray();
    }
}
