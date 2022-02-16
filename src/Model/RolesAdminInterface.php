<?php

namespace Softspring\UserBundle\Model;

interface RolesAdminInterface extends RolesInterface
{
    public function isAdmin(): bool;

    public function setAdmin(bool $admin): void;

    public function isSuperAdmin(): bool;

    public function setSuperAdmin(bool $superAdmin): void;
}
