<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\MediaBundle\Model\MediaInterface;
use Softspring\UserBundle\Model\UserMediaAvatarTrait as UserMediaAvatarTraitModel;

trait UserMediaAvatarTrait
{
    use UserMediaAvatarTraitModel;

    #[ORM\ManyToOne(targetEntity: "Softspring\MediaBundle\Model\MediaInterface", cascade: ['all'])]
    protected ?MediaInterface $avatarMedia = null;
}
