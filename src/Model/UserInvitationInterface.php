<?php

namespace Softspring\UserBundle\Model;

interface UserInvitationInterface
{
    public function getId();

    public function getUserIdentifier(): ?string;

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    public function getInvitationToken(): ?string;

    public function setInvitationToken(?string $invitationToken): void;

    public function getAcceptedAt(): ?\DateTime;

    public function setAcceptedAt(?\DateTime $acceptedAt): void;

    public function getInviter(): ?UserInterface;

    public function setInviter(?UserInterface $inviter): void;

    public function getUser(): ?UserInterface;

    public function setUser(?UserInterface $user): void;
}
