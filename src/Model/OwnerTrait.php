<?php

namespace Softspring\UserBundle\Model;

trait OwnerTrait
{
    protected ?UserInterface $owner = null;

    public function getOwner(): ?UserInterface
    {
        return $this->owner;
    }

    public function setOwner(?UserInterface $owner): void
    {
        $this->owner = $owner;
    }
}
