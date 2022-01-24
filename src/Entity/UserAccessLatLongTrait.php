<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait UserAccessLatLongTrait
{
    /**
     * @var float|null
     * @ORM\Column(name="location_lat", type="decimal", precision=20, scale=16, nullable=true)
     */
    protected $lat;

    /**
     * @var float|null
     * @ORM\Column(name="location_long", type="decimal", precision=20, scale=16, nullable=true)
     */
    protected $long;

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): void
    {
        $this->lat = $lat;
    }

    public function getLong(): ?float
    {
        return $this->long;
    }

    public function setLong(?float $long): void
    {
        $this->long = $long;
    }
}
