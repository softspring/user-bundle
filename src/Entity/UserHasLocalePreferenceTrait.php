<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\UserHasLocalePreferenceTrait as UserHasLocalePreferenceTraitModel;

trait UserHasLocalePreferenceTrait
{
    use UserHasLocalePreferenceTraitModel;

    #[ORM\Column(name: 'locale', type: 'string', length: 5, nullable: true, options: ['fixed' => true])]
    protected ?string $locale = null;
}
