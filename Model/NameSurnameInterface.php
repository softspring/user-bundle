<?php

namespace Softspring\UserBundle\Model;

interface NameSurnameInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getSurname(): ?string;

    /**
     * @param string|null $surname
     */
    public function setSurname(?string $surname): void;
}