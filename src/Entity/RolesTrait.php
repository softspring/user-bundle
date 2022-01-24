<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\Traits\RolesTrait as RolesTraitModel;

trait RolesTrait
{
    use RolesTraitModel;

    /**
     * @var array
     * @ORM\Column(name="roles", type="array", nullable=false, options={"default":[]})
     */
    protected $roles = [];
}
