<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\Traits\RolesAdminTrait as RolesAdminTraitModel;

trait RolesAdminTrait
{
    use RolesAdminTraitModel;

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
}
