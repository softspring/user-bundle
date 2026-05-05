<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\RolesTrait as RolesTraitModel;

trait RolesTrait
{
    use RolesTraitModel;

    #[ORM\Column(name: 'roles', type: 'json', nullable: false)]
    protected array $roles = [];
}
