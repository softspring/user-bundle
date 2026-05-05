<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Util;

interface TokenGeneratorInterface
{
    public function generateToken(): string;
}
