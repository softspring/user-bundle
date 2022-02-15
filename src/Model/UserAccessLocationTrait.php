<?php

namespace Softspring\UserBundle\Model;

trait UserAccessLocationTrait
{
    protected ?string $city;

    protected ?string $region;

    protected ?string $country;

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): void
    {
        $this->region = $region;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }
}
