<?php

namespace Softspring\UserBundle\Manager;

use Softspring\Component\CrudlController\Manager\CrudlEntityManagerInterface;
use Softspring\UserBundle\Model\UserAccessInterface;

interface UserAccessManagerInterface extends CrudlEntityManagerInterface
{
    /**
     * @return UserAccessInterface
     */
    public function createEntity(): object;

    /**
     * @param UserAccessInterface $entity
     */
    public function saveEntity(object $entity): void;

    /**
     * @param UserAccessInterface $entity
     */
    public function deleteEntity(object $entity): void;
}
