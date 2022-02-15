<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\UserAccessLatLongTrait as UserAccessLatLongTraitModel;

trait UserAccessLatLongTrait
{
    use UserAccessLatLongTraitModel;

    /**
     * @ORM\Column(name="location_lat", type="decimal", precision=20, scale=16, nullable=true)
     */
    protected ?float $lat = null;

    /**
     * @ORM\Column(name="location_long", type="decimal", precision=20, scale=16, nullable=true)
     */
    protected ?float $long = null;
}
