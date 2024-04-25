<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\EmailTrait as EmailTraitModel;

trait EmailTrait
{
    use EmailTraitModel;

    #[ORM\Column(name: 'email', type: 'string', length: 180, unique: true, nullable: false)]
    protected ?string $email = null;
}
