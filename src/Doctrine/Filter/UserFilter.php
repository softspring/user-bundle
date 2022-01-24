<?php

namespace Softspring\UserBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Softspring\UserBundle\Model\RolesAdminInterface;

class UserFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->getReflectionClass()->implementsInterface(RolesAdminInterface::class)) {
            return '';
        }

        return $targetTableAlias.'.is_admin = 0';
    }
}
