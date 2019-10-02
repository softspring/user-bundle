<?php

namespace Softspring\UserBundle\Entity\Oauth;

use Doctrine\ORM\Mapping as ORM;

trait FacebookOauthTrait
{
    /**
     * @var string|null
     * @ORM\Column(name="facebook_user_id", type="string", length=180, nullable=true)
     */
    protected $facebookUserId;

    /**
     * @return string|null
     */
    public function getFacebookUserId(): ?string
    {
        return $this->facebookUserId;
    }

    /**
     * @param string|null $facebookUserId
     */
    public function setFacebookUserId(?string $facebookUserId): void
    {
        $this->facebookUserId = $facebookUserId;
    }
}