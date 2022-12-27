<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserPasswordInterface extends SymfonyUserInterface, PasswordAuthenticatedUserInterface
{
    public function setSalt(?string $salt): void;

    public function getSalt(): ?string;

    public function setPassword(?string $password): void;

    public function getPassword(): ?string;

    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $plainPassword): void;
}
