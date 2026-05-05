<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Model;

interface SingleUserInterface
{
    public function getUser(): ?UserInterface;

    public function setUser(?UserInterface $user): void;
}
