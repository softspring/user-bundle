<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Softspring\UserBundle\Model\RolesAdminInterface;

class UserFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, mixed $targetTableAlias): string
    {
        if (!$targetEntity->getReflectionClass()->implementsInterface(RolesAdminInterface::class)) {
            return '';
        }

        return $targetTableAlias.'.is_admin = false';
    }
}
