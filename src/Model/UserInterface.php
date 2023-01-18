<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface
{
    /**
     * @see Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUserIdentifier(): string;

    public function getDisplayName(): ?string;

    public function __serialize(): array;

    public function __unserialize(array $data): void;
}
