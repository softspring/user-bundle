<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Event;

use Exception;
use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Event\RegisterExceptionEvent;
use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserEventsTest extends TestCase
{
    public function testUserEventExposesUserAndRequest(): void
    {
        $user = new User();
        $request = new Request();

        $event = new UserEvent($user, $request);

        $this->assertSame($user, $event->getUser());
        $this->assertSame($request, $event->getRequest());
    }

    public function testGetResponseUserEventStoresResponse(): void
    {
        $event = new GetResponseUserEvent(new User(), null);
        $response = new Response('registered');

        $this->assertNull($event->getResponse());

        $event->setResponse($response);

        $this->assertSame($response, $event->getResponse());
    }

    public function testUserInvitationEventExposesInvitationAndRequest(): void
    {
        $invitation = new ExampleInvitation();
        $request = new Request();

        $event = new UserInvitationEvent($invitation, $request);

        $this->assertSame($invitation, $event->getInvitation());
        $this->assertSame($request, $event->getRequest());
    }

    public function testRegisterExceptionEventCanOverrideThrowableException(): void
    {
        $form = $this->createMock(FormInterface::class);
        $exception = new Exception('registration failed');
        $event = new RegisterExceptionEvent($form, $exception);

        $this->assertSame($exception, $event->getException());
        $this->assertSame($exception, $event->getThrowException());

        $event->setThrowException(null);

        $this->assertNull($event->getThrowException());
    }
}
