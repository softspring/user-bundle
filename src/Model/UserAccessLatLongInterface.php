<?php

namespace Softspring\UserBundle\Model;

interface UserAccessLatLongInterface
{
    public function getLat(): ?float;

    public function setLat(?float $lat): void;

    public function getLong(): ?float;

    public function setLong(?float $long): void;
}
