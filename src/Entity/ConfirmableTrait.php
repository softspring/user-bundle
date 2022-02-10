<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ConfirmableTrait
{
    /**
     * @ORM\Column(name="confirmation_token", type="string", length=180, unique=true, nullable=true)
     */
    protected ?string $confirmationToken;

    /**
     * @ORM\Column(name="confirmed_at", type="integer", nullable=true, options={"unsigned":true})
     */
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
