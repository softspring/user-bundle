<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\UserPasswordTrait as UserPasswordTraitModel;

trait UserPasswordTrait
{
    use UserPasswordTraitModel;

    /**
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    protected ?string $salt = null;

    /**
     * @ORM\Column(name="password_encoded", type="string", nullable=true)
     */
    protected ?string $password = null;
}
