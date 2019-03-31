<?php

namespace Softspring\UserBundle\Model;

interface UserInvitationInterface
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
    public function getUsername(): ?string;

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
     * @param string|null $username
     */
    public function setUsername(?string $username): void;

    /**
     * @return string|null
     */
    public function getInvitationToken(): ?string;

    /**
     * @param string|null $invitationToken
     */
    public function setInvitationToken(?string $invitationToken): void;

    /**
     * @return array
     */
    public function getRoles(): array;

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void;

    /**
     * @return \DateTime|null
     */
    public function getAcceptedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $acceptedAt
     */
    public function setAcceptedAt(?\DateTime $acceptedAt): void;

    /**
     * @return UserInterface|null
     */
    public function getInviter(): ?UserInterface;

    /**
     * @param UserInterface|null $inviter
     */
    public function setInviter(?UserInterface $inviter): void;

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @param UserInterface|null $user
     */
    public function setUser(?UserInterface $user): void;
}