<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\UserIdentifierUsernameTrait as UserIdentifierUsernameTraitModel;

trait UserIdentifierUsernameTrait
{
    use UserIdentifierUsernameTraitModel;

    /**
     * @ORM\Column(name="username", type="string", nullable=false, unique=true)
     */
    protected ?string $username = null;
}
