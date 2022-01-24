<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserPasswordInterface extends SymfonyUserInterface
{
    public function setSalt(?string $salt): void;

    public function setPassword(?string $password): void;

    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $plainPassword): void;
}
