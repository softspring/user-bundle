<?php

namespace Softspring\UserBundle\Model;

/**
 * Class User.
 */
abstract class UserInvitation implements UserInvitationInterface
{
    protected ?UserInterface $inviter = null;

    protected ?UserInterface $user = null;

    protected ?string $email = null;

    protected ?string $invitationToken = null;

    protected ?int $acceptedAt = null;

    public function __toString(): string
    {
        return "{$this->getUserIdentifier()}";
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
        return null !== $this->acceptedAt ? \DateTime::createFromFormat('U', "{$this->acceptedAt}") : null;
    }

    public function setAcceptedAt(?\DateTime $acceptedAt): void
    {
        $this->acceptedAt = $acceptedAt instanceof \DateTime ? (int) $acceptedAt->format('U') : null;
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
