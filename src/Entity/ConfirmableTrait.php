<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Softspring\UserBundle\Model\ConfirmableTrait as ConfirmableTraitModel;

trait ConfirmableTrait
{
    use ConfirmableTraitModel;

    /**
     * @ORM\Column(name="confirmation_token", type="string", length=180, unique=true, nullable=true)
     */
    protected ?string $confirmationToken;

    /**
     * @ORM\Column(name="confirmed_at", type="integer", nullable=true, options={"unsigned":true})
     */
    protected ?int $confirmedAt;
}
