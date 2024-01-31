<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\PasswordRequestTrait as PasswordRequestTraitModel;

trait PasswordRequestTrait
{
    use PasswordRequestTraitModel;

    /**
     * @ORM\Column(name="password_request_token", type="string", length=180, unique=true, nullable=true)
     */
    #[ORM\Column(name: 'password_request_token', type: 'string', length: 180, unique: true, nullable: true)]
    protected ?string $passwordRequestToken = null;

    /**
     * @ORM\Column(name="password_requested_at", type="integer", nullable=true, options={"unsigned":true})
     */
    #[ORM\Column(name: 'password_requested_at', type: 'integer', nullable: true, options: ['unsigned' => true])]
    protected ?int $passwordRequestedAt = null;
}
