<?php

namespace Softspring\UserBundle\Model;

interface UserAvatarInterface
{
    public function getAvatarUrl(array $options = []): string;
}