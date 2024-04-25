<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\RolesAdminTrait as RolesAdminTraitModel;

trait RolesAdminTrait
{
    use RolesAdminTraitModel;

    #[ORM\Column(name: 'is_admin', type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $admin = false;

    #[ORM\Column(name: 'is_super_admin', type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $superAdmin = false;
}
