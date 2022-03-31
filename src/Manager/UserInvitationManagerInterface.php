<?php

namespace Softspring\UserBundle\Manager;

use Softspring\Component\CrudlController\Manager\CrudlEntityManagerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;

interface UserInvitationManagerInterface extends CrudlEntityManagerInterface
{
    public function createUser(UserInvitationInterface $invitation): UserInterface;

    public function findInvitationBy(array $criteria): ?UserInvitationInterface;

    public function findInvitationByToken(string $token): ?UserInvitationInterface;

    /**
     * @return UserInvitationInterface
     */
    public function createEntity(): object;

    /**
     * @param UserInvitationInterface $entity
     */
    public function saveEntity(object $entity): void;

    /**
     * @param UserInvitationInterface $entity
     */
    public function deleteEntity(object $entity): void;
}
