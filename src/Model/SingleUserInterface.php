<?php

namespace Softspring\UserBundle\Model;

use Softspring\UserBundle\Model\UserInterface;

interface SingleUserInterface
{
    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @param UserInterface|null $user
     */
    public function setUser(?UserInterface $user): void;
}