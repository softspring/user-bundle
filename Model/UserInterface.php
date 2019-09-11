<?php

namespace Softspring\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface, \Serializable
{
    /**
     * @inheritdoc
     */
    public function __toString(): string;

    /**
     * @return mixed|null
     */
    public function getId();

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void;

    /**
     * @return bool
     */
    public function isAdmin(): bool;

    /**
     * @param bool $admin
     */
    public function setAdmin(bool $admin): void;

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool;

    /**
     * @param bool $superAdmin
     */
    public function setSuperAdmin(bool $superAdmin): void;

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string;

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void;

    /**
     * @return \DateTime|null
     */
    public function getLastLogin(): ?\DateTime;

    /**
     * @param \DateTime|null $lastLogin
     */
    public function setLastLogin(?\DateTime $lastLogin): void;

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void;

    /**
     * @param string|null $salt
     */
    public function setSalt(?string $salt): void;

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void;

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void;
}