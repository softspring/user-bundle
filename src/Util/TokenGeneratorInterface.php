<?php

namespace Softspring\UserBundle\Util;

interface TokenGeneratorInterface
{
    public function generateToken(): string;
}
