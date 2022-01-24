<?php

namespace Softspring\UserBundle\Util;

class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * Based on FOS\UserBundle\Util\TokenGenerator.
     *
     * {@inheritdoc}
     */
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
