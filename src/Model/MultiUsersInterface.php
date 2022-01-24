<?php

namespace Softspring\UserBundle\Model;

use Doctrine\Common\Collections\Collection;

interface MultiUsersInterface
{
    /**
     * @return Collection|UserInterface[]
     */
    public function getUsers(): Collection;

    public function addUser(UserInterface $user): void;

    public function removeUser(UserInterface $user): void;
}
