<?php

namespace Softspring\UserBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Softspring\UserBundle\Model\UserInterface;

class AdminFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->getReflectionClass()->implementsInterface(UserInterface::class)) {
            return '';
        }

        return $targetTableAlias.'.is_admin = 1';
    }
}