<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait EnabledTrait
 */
trait EnabledTrait
{
    /**
     * @var bool
     * @ORM\Column(name="enabled", type="boolean", nullable=false, options={"default":false})
     */
    protected $enabled = false;

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}