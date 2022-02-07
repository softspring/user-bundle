<?php

namespace Softspring\UserBundle\Model;

/**
 * Class User.
 */
abstract class UserInvitation implements UserInvitationInterface
{
    /**
     * @var mixed|null
     */
    protected $id;

    protected ?UserInterface $inviter;

    protected ?UserInterface $user;

    protected ?string $username;

    protected ?string $email;

    protected ?string $invitationToken;

    protected ?int $acceptedAt;

    public function __toString(): string
    {
        return "{$this->getId()}";
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getInvitationToken(): ?string
    {
        return $this->invitationToken;
    }

    public function setInvitationToken(?string $invitationToken): void
    {
        $this->invitationToken = $invitationToken;
    }

    public function getAcceptedAt(): ?\DateTime
    {
        return \DateTime::createFromFormat('U', $this->acceptedAt) ?: null;
    }

    public function setAcceptedAt(?\DateTime $acceptedAt): void
    {
        $this->acceptedAt = $acceptedAt instanceof \DateTime ? $acceptedAt->format('U') : null;
    }

    public function getInviter(): ?UserInterface
    {
        return $this->inviter;
    }

    public function setInviter(?UserInterface $inviter): void
    {
        $this->inviter = $inviter;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }
}
