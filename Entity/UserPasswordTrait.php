<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\Traits\UserPasswordTrait as UserPasswordTraitModel;

trait UserPasswordTrait
{
    use UserPasswordTraitModel;

    /**
     * @var string|null
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    protected $salt;

    /**
     * @var string|null
     * @ORM\Column(name="password_encoded", type="string", nullable=true)
     */
    protected $password;
}