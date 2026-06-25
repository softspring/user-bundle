<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Model;

use DateTime;
use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Model\UserAccess;

class UserAccessTest extends TestCase
{
    public function testLoginAtCanBeStoredAndCleared(): void
    {
        $access = new TestingModelUserAccess();

        $access->setLoginAt(new DateTime('@1700000000'));
        $this->assertSame('1700000000', $access->getLoginAt()->format('U'));

        $access->setLoginAt(null);
        $this->assertNull($access->getLoginAt());
    }

    public function testOperatingSystemVendorDetection(): void
    {
        $access = new TestingModelUserAccess();

        $access->setUserAgent('Windows NT');
        $this->assertSame('microsoft', $access->getOperatingSystemVendor());

        $access->setUserAgent('Macintosh');
        $this->assertSame('apple', $access->getOperatingSystemVendor());

        $access->setUserAgent('Linux x86_64');
        $this->assertSame('linux', $access->getOperatingSystemVendor());

        $access->setUserAgent('Unknown');
        $this->assertSame('', $access->getOperatingSystemVendor());
    }
}

class TestingModelUserAccess extends UserAccess
{
}
