<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface, \Serializable
{
    public function setUsername(?string $username): void;

    /**
     * @see Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUserIdentifier(): string;
}
