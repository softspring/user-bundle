<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserPasswordInterface extends SymfonyUserInterface
{
    /**
     * @param string|null $salt
     */
    public function setSalt(?string $salt): void;

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void;

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string;

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void;
}