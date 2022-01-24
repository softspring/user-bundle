<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface RolesInterface extends SymfonyUserInterface
{
    public function setRoles(array $roles): void;
}
