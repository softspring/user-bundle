<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Manipulator;

use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Manipulator\UserAccessManipulator;
use Softspring\UserBundle\Model\UserAccess;
use Softspring\UserBundle\Model\UserAccessLatLongInterface;
use Softspring\UserBundle\Model\UserAccessLatLongTrait;
use Softspring\UserBundle\Model\UserAccessLocationInterface;
use Softspring\UserBundle\Model\UserAccessLocationTrait;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserAccessManipulatorTest extends TestCase
{
    public function testRegistersBasicAccessData(): void
    {
        $access = new TestingUserAccess();
        $manager = $this->createMock(UserAccessManagerInterface::class);
        $manager->expects($this->once())->method('createEntity')->willReturn($access);
        $manager->expects($this->once())->method('saveEntity')->with($access);

        $request = Request::create('/', 'GET', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'Mozilla Linux',
        ]);
        $user = new User();

        $registered = (new UserAccessManipulator($manager))->register($user, $request);

        $this->assertSame($access, $registered);
        $this->assertSame($user, $access->getUser());
        $this->assertSame('127.0.0.1', $access->getIp());
        $this->assertSame('Mozilla Linux', $access->getUserAgent());
        $this->assertSame('linux', $access->getOperatingSystemVendor());
        $this->assertSame(date('Y-m-d'), $access->getLoginAt()->format('Y-m-d'));
    }

    public function testRegistersGoogleAppEngineLocationData(): void
    {
        $access = new TestingUserAccess();
        $manager = $this->createMock(UserAccessManagerInterface::class);
        $manager->method('createEntity')->willReturn($access);

        $request = Request::create('/', 'GET', [], [], [], [
            'GAE_APPLICATION' => 'app',
            'HTTP_X_APPENGINE_CITY' => 'Madrid',
            'HTTP_X_APPENGINE_REGION' => 'M',
            'HTTP_X_APPENGINE_COUNTRY' => 'ES',
            'HTTP_X_APPENGINE_CITYLATLONG' => '40.416775,-3.703790',
        ]);

        (new UserAccessManipulator($manager))->register(new User(), $request);

        $this->assertSame('Madrid', $access->getCity());
        $this->assertSame('M', $access->getRegion());
        $this->assertSame('ES', $access->getCountry());
        $this->assertSame(40.416775, $access->getLat());
        $this->assertSame(-3.703790, $access->getLong());
    }
}

class TestingUserAccess extends UserAccess implements UserAccessLocationInterface, UserAccessLatLongInterface
{
    use UserAccessLocationTrait;
    use UserAccessLatLongTrait;
}
