<?php

namespace Softspring\UserBundle\Model;

interface PasswordRequestInterface
{
    /**
     * @return \DateTime|null
     */
    public function getPasswordRequestedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $passwordRequestedAt
     */
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): void;

    /**
     * @return string|null
     */
    public function getPasswordRequestToken(): ?string;

    /**
     * @param string|null $passwordRequestToken
     */
    public function setPasswordRequestToken(?string $passwordRequestToken): void;
}