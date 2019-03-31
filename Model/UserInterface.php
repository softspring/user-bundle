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
     * @param string|null $id
     */
    public function setId(?string $id): void;

    /**
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void;

    /**
     * @return null|string
     */
    public function getSurname(): ?string;

    /**
     * @param null|string $surname
     */
    public function setSurname(?string $surname): void;

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
     * @return \DateTime|null
     */
    public function getPasswordRequestedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $passwordRequestedAt
     */
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): void;

    /**
     * @return string|null
     */
    public function getPasswordRequestToken(): ?string;

    /**
     * @param string|null $passwordRequestToken
     */
    public function setPasswordRequestToken(?string $passwordRequestToken): void;

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
     * @return null|string
     */
    public function getConfirmationToken(): ?string;

    /**
     * @param null|string $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void;

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void;

    /**
     * @return \DateTime|null
     */
    public function getConfirmedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $confirmedAt
     */
    public function setConfirmedAt(?\DateTime $confirmedAt): void;
}