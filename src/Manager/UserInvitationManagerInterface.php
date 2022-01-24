<?php

namespace Softspring\UserBundle\Manager;

use Softspring\CrudlBundle\Manager\CrudlEntityManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;

interface UserInvitationManagerInterface extends CrudlEntityManagerInterface
{
    /**
     * @deprecated
     */
    public function create(): UserInvitationInterface;

    public function createUser(UserInvitationInterface $invitation): UserInterface;

    /**
     * @deprecated
     */
    public function save(UserInvitationInterface $invitation): void;

    public function findInvitationBy(array $criteria): ?UserInvitationInterface;

    public function findInvitationByToken(string $token): ?UserInvitationInterface;

    /**
     * @return UserInvitationInterface
     */
    public function createEntity();

    /**
     * @param UserInvitationInterface $entity
     */
    public function saveEntity($entity): void;

    /**
     * @param UserInvitationInterface $entity
     */
    public function deleteEntity($entity): void;
}
