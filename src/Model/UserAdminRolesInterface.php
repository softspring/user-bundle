<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserAdminRolesInterface extends SymfonyUserInterface
{
    /**
     * @return bool
     */
    public function isAdmin(): bool;

    /**
     * @param bool $admin
     */
    public function setAdmin(bool $admin): void;

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool;

    /**
     * @param bool $superAdmin
     */
    public function setSuperAdmin(bool $superAdmin): void;

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void;
}