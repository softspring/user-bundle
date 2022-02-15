<?php

namespace Softspring\UserBundle\Model;

trait EnabledTrait
{
    protected bool $enabled = false;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
