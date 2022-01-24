<?php

namespace Softspring\UserBundle\Manager;

use Softspring\CrudlBundle\Manager\CrudlEntityManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserInterface;

interface UserManagerInterface extends CrudlEntityManagerInterface
{
    public function findUserBy(array $criteria): ?UserInterface;

    public function findUserByUsername(string $username): ?UserInterface;

    public function findUserByEmail(string $email): ?UserInterface;

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface;

    /**
     * @param string $token
     *
     * @return UserInterface|ConfirmableInterface|null
     */
    public function findUserByConfirmationToken($token);

    /**
     * @return UserInterface
     */
    public function createEntity();

    /**
     * @param UserInterface $entity
     */
    public function saveEntity($entity): void;

    /**
     * @param UserInterface $entity
     */
    public function deleteEntity($entity): void;
}
