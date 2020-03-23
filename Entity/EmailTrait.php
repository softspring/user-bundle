<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait EmailTrait
 */
trait EmailTrait
{
    /**
     * @var string|null
     * @ORM\Column(name="email", type="string", length=180, unique=true, nullable=false)
     */
    protected $email;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}