<?php

namespace Softspring\UserBundle\Manager;

use Softspring\CrudlBundle\Manager\CrudlEntityManagerInterface;
use Softspring\UserBundle\Model\UserAccessInterface;

interface UserAccessManagerInterface extends CrudlEntityManagerInterface
{
    /**
     * @return UserAccessInterface
     */
    public function createEntity();

    /**
     * @param UserAccessInterface $entity
     */
    public function saveEntity($entity): void;

    /**
     * @param UserAccessInterface $entity
     */
    public function deleteEntity($entity): void;
}