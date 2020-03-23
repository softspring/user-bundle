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

    /**
     * @param UserInvitationInterface $invitation
     *
     * @return UserInterface
     */
    public function createUser(UserInvitationInterface $invitation): UserInterface;

    /**
     * @deprecated
     */
    public function save(UserInvitationInterface $invitation): void;

    /**
     * @param array $criteria
     *
     * @return UserInvitationInterface|null
     */
    public function findInvitationBy(array $criteria): ?UserInvitationInterface;

    /**
     * @param string $token
     *
     * @return UserInvitationInterface|null
     */
    public function findInvitationByToken(string $token): ?UserInvitationInterface;
}