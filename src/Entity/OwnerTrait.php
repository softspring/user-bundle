<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\OwnerTrait as OwnerTraitModel;

trait OwnerTrait
{
    use OwnerTraitModel;

    /**
     * @ORM\ManyToOne(targetEntity="Softspring\UserBundle\Model\UserInterface", cascade={"all"})
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected ?UserInterface $owner;
}
