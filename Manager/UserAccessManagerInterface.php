<?php

namespace Softspring\UserBundle\Manager;

use Softspring\UserBundle\Model\UserAccessInterface;

interface UserAccessManagerInterface
{
    /**
     * @inheritdoc
     */
    public function getClass(): string;

    /**
     * @inheritdoc
     */
    public function create(): UserAccessInterface;

    /**
     * @param UserAccessInterface $userAccess
     */
    public function save(UserAccessInterface $userAccess): void;
}