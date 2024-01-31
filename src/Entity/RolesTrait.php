<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\RolesTrait as RolesTraitModel;

trait RolesTrait
{
    use RolesTraitModel;

    /**
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    #[ORM\Column(name: 'roles', type: 'array', nullable: false)]
    protected array $roles = [];
}
