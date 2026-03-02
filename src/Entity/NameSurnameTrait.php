<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\NameSurnameTrait as NameSurnameTraitModel;

trait NameSurnameTrait
{
    use NameSurnameTraitModel;

    #[ORM\Column(name: 'name', type: 'string', length: 50, nullable: true)]
    protected ?string $name = null;

    #[ORM\Column(name: 'surname', type: 'string', length: 80, nullable: true)]
    protected ?string $surname = null;
}
