<?php

namespace Softspring\UserBundle\Util;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken(): string;
}
