<?php

namespace Softspring\UserBundle\Entity;

use Softspring\UserBundle\Model\Traits\RolesFullTrait as RolesFullTraitModel;

trait RolesFullTrait
{
    use RolesAdminTrait, RolesTrait, RolesFullTraitModel {
        RolesFullTraitModel::getRoles insteadof RolesTrait;
        RolesFullTraitModel::setRoles insteadof RolesTrait;
    }
}
