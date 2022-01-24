<?php

namespace Softspring\UserBundle\Model\Oauth;

interface FacebookOauthInterface
{
    public function getFacebookUserId(): ?string;

    public function setFacebookUserId(?string $facebookUserId): void;
}
