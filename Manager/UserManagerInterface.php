<?php

namespace Softspring\UserBundle\Manager;

use Doctrine\ORM\EntityRepository;
use Softspring\CrudlBundle\Manager\CrudlEntityManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserInterface;

interface UserManagerInterface extends CrudlEntityManagerInterface
{
    /**
     * @param array $criteria
     *
     * @return UserInterface|null
     */
    public function findUserBy(array $criteria): ?UserInterface;

    /**
     * @param string $username
     *
     * @return UserInterface|null
     */
    public function findUserByUsername(string $username): ?UserInterface;

    /**
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function findUserByEmail(string $email): ?UserInterface;

    /**
     * @param string $usernameOrEmail
     *
     * @return UserInterface|null
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface;

    /**
     * @param string $token
     *
     * @return UserInterface|ConfirmableInterface|null
     */
    public function findUserByConfirmationToken($token);
}