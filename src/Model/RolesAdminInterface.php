<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface RolesAdminInterface extends SymfonyUserInterface
{
    public function isAdmin(): bool;

    public function setAdmin(bool $admin): void;

    public function isSuperAdmin(): bool;

    public function setSuperAdmin(bool $superAdmin): void;
}
