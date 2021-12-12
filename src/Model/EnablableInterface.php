<?php

namespace Softspring\UserBundle\Model;

interface EnablableInterface
{
    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void;
}