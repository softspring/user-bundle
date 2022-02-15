<?php

namespace Softspring\UserBundle\Model;

trait UserAccessLatLongTrait
{
    protected ?float $lat = null;

    protected ?float $long = null;

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
