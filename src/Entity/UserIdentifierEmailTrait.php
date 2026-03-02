<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\UserIdentifierEmailTrait as UserIdentifierEmailTraitModel;

trait UserIdentifierEmailTrait
{
    use UserIdentifierEmailTraitModel;

    #[ORM\Column(name: 'email', type: 'string', unique: true, nullable: false)]
    protected ?string $email = null;
}
