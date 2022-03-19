<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\UserAvatarTrait as UserAvatarTraitModel;

trait UserAvatarTrait
{
    use UserAvatarTraitModel;

    /**
     * @ORM\Column(name="avatar_url", type="string", nullable=true)
     */
    protected ?string $avatarUrl = null;
}
