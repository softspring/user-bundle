<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Model;

interface EnablableInterface
{
    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): void;
}
