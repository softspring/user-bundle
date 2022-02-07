<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PasswordRequestTrait
{
    /**
     * @var int|null
     * @ORM\Column(name="password_request_token", type="string", length=180, unique=true, nullable=true)
     */
    protected $passwordRequestToken;

    /**
     * @var string|null
     * @ORM\Column(name="password_requested_at", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $passwordRequestedAt;

    public function getPasswordRequestedAt(): ?\DateTime
    {
        return \DateTime::createFromFormat('U', $this->passwordRequestedAt) ?: null;
    }

    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt instanceof \DateTime ? $passwordRequestedAt->format('U') : null;
    }

    public function getPasswordRequestToken(): ?string
    {
        return $this->passwordRequestToken;
    }

    public function setPasswordRequestToken(?string $passwordRequestToken): void
    {
        $this->passwordRequestToken = $passwordRequestToken;
    }
}