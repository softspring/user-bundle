<?php

namespace Softspring\UserBundle\Model\Oauth;

interface FacebookOauthInterface
{
    /**
     * @return string|null
     */
    public function getFacebookUserId(): ?string;

    /**
     * @param string|null $facebookUserId
     */
    public function setFacebookUserId(?string $facebookUserId): void;
}