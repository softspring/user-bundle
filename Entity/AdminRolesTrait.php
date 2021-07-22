<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\Traits\AdminRolesTrait as AdminRolesTraitModel;

trait AdminRolesTrait
{
    use AdminRolesTraitModel;

    /**
     * @var bool
     * @ORM\Column(name="is_admin", type="boolean", nullable=false, options={"default":false})
     */
    protected $admin = false;

    /**
     * @var bool
     * @ORM\Column(name="is_super_admin", type="boolean", nullable=false, options={"default":false})
     */
    protected $superAdmin = false;

    /**
     * @var array
     * @ORM\Column(name="roles", type="array", nullable=false, options={"default":[]})
     */
    protected $roles = [];
}