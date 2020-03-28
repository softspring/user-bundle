<?php

namespace Softspring\UserBundle\Model;

interface UserAccessLocationInterface
{
    /**
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void;

    /**
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * @param string|null $country
     */
    public function setCountry(?string $country): void;
}