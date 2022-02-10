<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\Traits\UserLastLoginTrait as UserLastLoginTraitModel;

trait UserLastLoginTrait
{
    use UserLastLoginTraitModel;

    /**
     * @ORM\Column(name="last_login", type="integer", nullable=true, options={"unsigned":true})
     */
    protected ?int $lastLogin;
}
