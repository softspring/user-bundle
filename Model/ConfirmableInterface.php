<?php

namespace Softspring\UserBundle\Model;

interface ConfirmableInterface
{
    /**
     * @return string|null
     */
    public function getConfirmationToken(): ?string;

    /**
     * @param string|null $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void;

    /**
     * @return \DateTime|null
     */
    public function getConfirmedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $confirmedAt
     */
    public function setConfirmedAt(?\DateTime $confirmedAt): void;
}