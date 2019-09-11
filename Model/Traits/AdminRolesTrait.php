<?php

namespace Softspring\UserBundle\Model\Traits;

use Doctrine\Common\Collections\ArrayCollection;

trait AdminRolesTrait
{
    /**
     * @var bool
     */
    protected $admin = false;

    /**
     * @var bool
     */
    protected $superAdmin = false;

    /**
     * @var array
     */
    protected $roles = [];

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * @param bool $admin
     */
    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;

        if (!$admin) {
            $this->setSuperAdmin(false);
        }
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->superAdmin;
    }

    /**
     * @param bool $superAdmin
     */
    public function setSuperAdmin(bool $superAdmin): void
    {
        $this->superAdmin = $superAdmin;

        if ($superAdmin) {
            $this->setAdmin(true);
        }
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
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

    /**
     * @param array $roles
     */
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