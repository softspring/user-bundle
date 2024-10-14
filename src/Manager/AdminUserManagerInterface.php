<?php

namespace Softspring\UserBundle\Manager;

use Exception;
use Softspring\Component\CrudlController\Manager\CrudlEntityManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserInterface;

interface AdminUserManagerInterface extends CrudlEntityManagerInterface
{
    /**
     * @return UserInterface|ConfirmableInterface|null
     */
    public function findUserBy(array $criteria): ?UserInterface;

    public function findUserByIdentifier(string $identifier): ?UserInterface;

    public function findUserByConfirmationToken(string $token): ?ConfirmableInterface;

    /**
     * @return UserInterface
     */
    public function createEntity(): object;

    /**
     * @param UserInterface $entity
     *
     * @throws Exception
     */
    public function saveEntity(object $entity): void;

    /**
     * @param UserInterface $entity
     */
    public function deleteEntity(object $entity): void;
}
