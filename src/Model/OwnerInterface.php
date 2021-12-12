<?php

namespace Softspring\UserBundle\Model;

interface OwnerInterface
{
    /**
     * @return UserInterface|null
     */
    public function getOwner(): ?UserInterface;

    /**
     * @param UserInterface|null $owner
     */
    public function setOwner(?UserInterface $owner): void;
}