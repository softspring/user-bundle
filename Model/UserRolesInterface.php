<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserRolesInterface extends SymfonyUserInterface
{
    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void;

    /**
     * @return array
     */
    public function getRoles(): array;
}