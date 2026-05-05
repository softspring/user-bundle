<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Model;

interface OwnerInterface
{
    public function getOwner(): ?UserInterface;

    public function setOwner(?UserInterface $owner): void;
}
