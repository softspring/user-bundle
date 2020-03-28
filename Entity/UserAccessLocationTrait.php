<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait UserAccessLocationTrait
{
    /**
     * @var string|null
     * @ORM\Column(name="location_city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var string|null
     * @ORM\Column(name="location_country", type="string", length=2, nullable=true, options={"fixed":true})
     */
    protected $country;

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }
}