<?php

namespace Softspring\UserBundle\Model;

interface UserAccessLatLongInterface
{
    /**
     * @return float|null
     */
    public function getLat(): ?float;

    /**
     * @param float|null $lat
     */
    public function setLat(?float $lat): void;

    /**
     * @return float|null
     */
    public function getLong(): ?float;

    /**
     * @param float|null $long
     */
    public function setLong(?float $long): void;
}