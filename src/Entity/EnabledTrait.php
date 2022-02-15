<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\EnabledTrait as EnabledTraitModel;

trait EnabledTrait
{
    use EnabledTraitModel;

    /**
     * @ORM\Column(name="enabled", type="boolean", nullable=false, options={"default":false})
     */
    protected bool $enabled = false;
}
