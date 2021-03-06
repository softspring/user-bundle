<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\UserInterface;

trait OwnerTrait
{
    /**
     * @var UserInterface|null
     * @ORM\ManyToOne(targetEntity="Softspring\UserBundle\Model\UserInterface", cascade={"all"})
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @return UserInterface|null
     */
    public function getOwner(): ?UserInterface
    {
        return $this->owner;
    }

    /**
     * @param UserInterface|null $owner
     */
    public function setOwner(?UserInterface $owner): void
    {
        $this->owner = $owner;
    }
}