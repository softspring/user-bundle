<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Model;

interface UserAvatarInterface
{
    public function getAvatarUrl(array $options = []): string;
}
