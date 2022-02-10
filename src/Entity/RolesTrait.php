<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\Traits\RolesTrait as RolesTraitModel;

trait RolesTrait
{
    use RolesTraitModel;

    /**
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    protected array $roles = [];
}
