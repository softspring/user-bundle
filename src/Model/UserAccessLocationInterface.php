<?php

namespace Softspring\UserBundle\Model;

interface UserAccessLocationInterface
{
    public function getCity(): ?string;

    public function setCity(?string $city): void;

    public function getRegion(): ?string;

    public function setRegion(?string $region): void;

    public function getCountry(): ?string;

    public function setCountry(?string $country): void;
}
