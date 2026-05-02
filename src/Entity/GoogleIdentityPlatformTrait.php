<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\GoogleIdentityPlatformTrait as GoogleIdentityPlatformTraitModel;

trait GoogleIdentityPlatformTrait
{
    use GoogleIdentityPlatformTraitModel;

    #[ORM\Column(length: 32, nullable: true)]
    protected ?string $authSource = null;

    #[ORM\Column(length: 191, unique: true, nullable: true)]
    protected ?string $identityPlatformUserId = null;

    #[ORM\Column(length: 64, nullable: true)]
    protected ?string $identityProvider = null;

    #[ORM\Column(length: 191, nullable: true)]
    protected ?string $identityProviderUserId = null;
}
