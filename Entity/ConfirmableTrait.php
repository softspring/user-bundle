<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ConfirmableTrait
{
    /**
     * @var string|null
     * @ORM\Column(name="confirmation_token", type="string", length=180, unique=true, nullable=true)
     */
    protected $confirmationToken;

    /**
     * @var int|null
     * @ORM\Column(name="confirmed_at", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $confirmedAt;

    /**
     * @return string|null
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string|null $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return \DateTime|null
     */
    public function getConfirmedAt(): ?\DateTime
    {
        return \DateTime::createFromFormat("U", $this->confirmedAt) ?: null;
    }

    /**
     * @param \DateTime|null $confirmedAt
     */
    public function setConfirmedAt(?\DateTime $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt instanceof \DateTime ? $confirmedAt->format('U') : null;
    }
}