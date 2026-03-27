<?php

namespace Softspring\UserBundle\Model;

use DateTime;

trait ConfirmableTrait
{
    protected ?string $confirmationToken = null;

    protected ?int $confirmedAt = null;

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getConfirmedAt(): ?DateTime
    {
        return $this->confirmedAt ? DateTime::createFromFormat('U', (string) $this->confirmedAt) : null;
    }

    public function setConfirmedAt(?DateTime $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt instanceof DateTime ? (int) $confirmedAt->format('U') : null;
    }

    public function isConfirmed(): bool
    {
        return (bool) $this->confirmedAt;
    }
}
