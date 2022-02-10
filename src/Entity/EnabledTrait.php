<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait EnabledTrait.
 */
trait EnabledTrait
{
    /**
     * @ORM\Column(name="enabled", type="boolean", nullable=false, options={"default":false})
     */
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
