<?php

namespace Softspring\UserBundle\Model;

trait ConfirmableTrait
{
    protected ?string $confirmationToken;

    protected ?int $confirmedAt;

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getConfirmedAt(): ?\DateTime
    {
        return \DateTime::createFromFormat('U', $this->confirmedAt) ?: null;
    }

    public function setConfirmedAt(?\DateTime $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt instanceof \DateTime ? $confirmedAt->format('U') : null;
    }

    public function isConfirmed(): bool
    {
        return (bool) $this->confirmedAt;
    }
}
