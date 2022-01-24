<?php

namespace Softspring\UserBundle\Model;

interface NameSurnameInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getSurname(): ?string;

    public function setSurname(?string $surname): void;
}
