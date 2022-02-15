<?php

namespace Softspring\UserBundle\Entity;

use Softspring\UserBundle\Model\RolesFullTrait as RolesFullTraitModel;

trait RolesFullTrait
{
    use RolesAdminTrait, RolesTrait, RolesFullTraitModel {
        RolesFullTraitModel::isAdmin insteadof RolesTrait;
        RolesFullTraitModel::setAdmin insteadof RolesTrait;
        RolesFullTraitModel::isSuperAdmin insteadof RolesTrait;
        RolesFullTraitModel::setSuperAdmin insteadof RolesTrait;
        RolesFullTraitModel::getRoles insteadof RolesTrait;
        RolesFullTraitModel::setRoles insteadof RolesTrait;
        RolesFullTraitModel::isAdmin insteadof RolesAdminTrait;
        RolesFullTraitModel::setAdmin insteadof RolesAdminTrait;
        RolesFullTraitModel::isSuperAdmin insteadof RolesAdminTrait;
        RolesFullTraitModel::setSuperAdmin insteadof RolesAdminTrait;
        RolesFullTraitModel::getRoles insteadof RolesAdminTrait;
        RolesFullTraitModel::setRoles insteadof RolesAdminTrait;
    }
}
