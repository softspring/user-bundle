<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait UserAccessLocationTrait
{
    /**
     * @ORM\Column(name="location_city", type="string", length=255, nullable=true)
     */
    protected ?string $city;

    /**
     * @ORM\Column(name="location_region", type="string", length=2, nullable=true, options={"fixed":true})
     */
    protected ?string $region;

    /**
     * @ORM\Column(name="location_country", type="string", length=2, nullable=true, options={"fixed":true})
     */
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
