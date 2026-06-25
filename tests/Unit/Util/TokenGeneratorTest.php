<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Util;

use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Util\TokenGenerator;

class TokenGeneratorTest extends TestCase
{
    public function testGenerateTokenReturnsUrlSafeToken(): void
    {
        $token = (new TokenGenerator())->generateToken();

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+$/', $token);
        $this->assertSame(43, strlen($token));
    }
}
