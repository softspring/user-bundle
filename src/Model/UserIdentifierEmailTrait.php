<?php

namespace Softspring\UserBundle\Model;

trait UserIdentifierEmailTrait
{
    protected ?string $email = null;

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

    public function getUserIdentifier(): ?string
    {
        return $this->getEmail();
    }

    protected function getIdentifierField(): string
    {
        return 'email';
    }
}