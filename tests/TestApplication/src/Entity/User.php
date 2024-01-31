<?php

namespace Softspring\UserBundle\Tests\TestApplication\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Entity\NameSurnameTrait;
use Softspring\UserBundle\Entity\PasswordRequestTrait;
use Softspring\UserBundle\Entity\RolesFullTrait;
use Softspring\UserBundle\Entity\UserHasLocalePreferenceTrait;
use Softspring\UserBundle\Entity\UserIdentifierEmailTrait;
use Softspring\UserBundle\Entity\UserLastLoginTrait;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\PasswordRequestInterface;
use Softspring\UserBundle\Model\RolesFullInterface;
use Softspring\UserBundle\Model\User as UserModel;
use Softspring\UserBundle\Model\UserHasLocalePreferenceInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;

/**
 * @ORM\Entity
 */
#[ORM\Entity]
class User extends UserModel implements NameSurnameInterface, PasswordRequestInterface, UserWithEmailInterface, UserHasLocalePreferenceInterface, RolesFullInterface
{
    use NameSurnameTrait;
    use PasswordRequestTrait;
    use UserIdentifierEmailTrait;
    use UserHasLocalePreferenceTrait;
    use RolesFullTrait;
    use UserLastLoginTrait;

    /**
     * @ORM\Column(type="string", nullable=false, length=32)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    #[ORM\Id]
    #[ORM\Column(type: "string", nullable: false, length: 32)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    protected ?string $id = null;

    public function __construct()
    {
        parent::__construct();
        $this->id = uniqid();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDisplayName(): ?string
    {
        return $this->getName();
    }
}