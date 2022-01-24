<?php

namespace Softspring\UserBundle\Model;

interface OwnerInterface
{
    public function getOwner(): ?UserInterface;

    public function setOwner(?UserInterface $owner): void;
}
