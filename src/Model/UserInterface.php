<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface, \Serializable
{
    /**
     * @see Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUserIdentifier(): ?string;

    public function getDisplayName(): ?string;
}
