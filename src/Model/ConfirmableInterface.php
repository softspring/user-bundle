<?php

namespace Softspring\UserBundle\Model;

use DateTime;

interface ConfirmableInterface
{
    public function getConfirmationToken(): ?string;

    public function setConfirmationToken(?string $confirmationToken): void;

    public function getConfirmedAt(): ?DateTime;

    public function setConfirmedAt(?DateTime $confirmedAt): void;

    public function isConfirmed(): bool;
}
