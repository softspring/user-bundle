<?php

namespace Softspring\UserBundle\Model;

use Doctrine\Common\Collections\Collection;

interface MultiUsersInterface
{
    /**
     * @return Collection|UserInterface[]
     */
    public function getUsers(): Collection;

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user): void;

    /**
     * @param UserInterface $user
     */
    public function removeUser(UserInterface $user): void;
}