<?php

namespace Softspring\UserBundle\Model;

interface EnablableInterface
{
    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): void;
}
