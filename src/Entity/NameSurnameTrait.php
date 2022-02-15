<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\NameSurnameTrait as NameSurnameTraitModel;

trait NameSurnameTrait
{
    use NameSurnameTraitModel;

    /**
     * @ORM\Column(name="name", type="string", nullable=true, length=50)
     */
    protected ?string $name;

    /**
     * @ORM\Column(name="surname", type="string", nullable=true, length=80)
     */
    protected ?string $surname;
}
