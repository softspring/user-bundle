<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\UserAccessLocationTrait as UserAccessLocationTraitModel;

trait UserAccessLocationTrait
{
    use UserAccessLocationTraitModel;

    /**
     * @ORM\Column(name="location_city", type="string", length=255, nullable=true)
     */
    protected ?string $city = null;

    /**
     * @ORM\Column(name="location_region", type="string", length=2, nullable=true, options={"fixed":true})
     */
    protected ?string $region = null;

    /**
     * @ORM\Column(name="location_country", type="string", length=2, nullable=true, options={"fixed":true})
     */
    protected ?string $country = null;
}
