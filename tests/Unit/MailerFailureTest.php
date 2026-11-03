<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Softspring\Component\Events\GetResponseFormEvent;
use Softspring\UserBundle\Controller\Settings\ConfirmationController;
use Softspring\UserBundle\EventListener\SendResetPasswordEmailListener;
use Softspring\UserBundle\Model\PasswordRequestInterface;
use Symfony\Component\Form\FormInterface;
use Softspring\UserBundle\Controller\Admin\InvitationsController;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Event\GetResponseUserInvitationEvent;
use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\EventListener\Admin\FlashMessagesListener;
use Softspring\UserBundle\EventListener\EmailInvitationListener;
use Softspring\UserBundle\EventListener\UserRegistrationListener;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Util\TokenGenerator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

class MailerFailureTest extends TestCase
{
    #[DataProvider('invitationResults')]
    public function testResendInvitationWithoutUser(bool $failure, string $locale): void
    {
        $invitation = $this->createStub(UserInvitationInterface::class);
        $invitation->method('getUser')->willReturn(null);
        $invitation->method('getId')->willReturn(42);
        $invitation->method('getEmail')->willReturn('invited@example.com');
        $manager = $this->createMock(UserInvitationManagerInterface::class);
        $manager->expects(self::once())->method('findInvitationBy')->with(['id' => 42])->willReturn($invitation);
        $mailer = $this->createMock(UserMailerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        if ($failure) {
            $exception = new RuntimeException('Transport unavailable');
            $mailer->expects(self::once())->method('sendInvitationEmail')->with($invitation)->willThrowException($exception);
            $logger->expects(self::once())->method('error')->with('Failed to resend invitation email.', ['exception' => $exception, 'invitation_id' => 42]);
        } else {
            $mailer->expects(self::once())->method('sendInvitationEmail')->with($invitation);
            $logger->expects(self::never())->method('error');
        }

        $translator = new Translator($locale);
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource('yaml', __DIR__.'/../../translations/sfs_user.'.$locale.'.yaml', $locale, 'sfs_user');
        $flashes = new FlashBag();
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new FlashMessagesListener($flashes, $translator));
        $eventName = $failure ? SfsUserEvents::ADMIN_INVITATIONS_RESEND_ERROR : SfsUserEvents::ADMIN_INVITATIONS_RESEND_SUCCESS;
        $dispatcher->addListener($eventName, function (GetResponseUserInvitationEvent $event) use ($invitation): void {
            self::assertSame($invitation, $event->getInvitation());
        });

        $router = $this->createMock(UrlGeneratorInterface::class);
        $router->expects(self::once())->method('generate')->with('sfs_user_admin_invitations_details', ['invitation' => $invitation])->willReturn('/invitations/42');
        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->with('router')->willReturn(true);
        $container->expects(self::once())->method('get')->with('router')->willReturn($router);
        $controller = new InvitationsController($manager, $mailer, $dispatcher, $logger);
        $controller->setContainer($container);
        $request = new Request();
        $request->setLocale($locale);

        self::assertSame('/invitations/42', $controller->resendEmail(42, $request)->headers->get('Location'));
        $messages = $flashes->get($failure ? 'error' : 'success');
        self::assertCount(1, $messages);
        self::assertStringNotContainsString('admin_users.', $messages[0]);
        if (!$failure) {
            self::assertStringContainsString('invited@example.com', $messages[0]);
        }
    }

    public static function invitationResults(): array
    {
        return [[false, 'en'], [true, 'en'], [false, 'es'], [true, 'es']];
    }

    public function testMissingInvitationReturns404(): void
    {
        $manager = $this->createMock(UserInvitationManagerInterface::class);
        $manager->expects(self::once())->method('findInvitationBy')->willReturn(null);
        $mailer = $this->createMock(UserMailerInterface::class);
        $mailer->expects(self::never())->method('sendInvitationEmail');
        $controller = new InvitationsController($manager, $mailer, new EventDispatcher());

        $this->expectException(NotFoundHttpException::class);
        $controller->resendEmail(42, new Request());
    }

    public function testResendEventCanOverrideResponse(): void
    {
        $invitation = $this->createStub(UserInvitationInterface::class);
        $manager = $this->createMock(UserInvitationManagerInterface::class);
        $manager->expects(self::once())->method('findInvitationBy')->willReturn($invitation);
        $response = new Response('Custom response');
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(SfsUserEvents::ADMIN_INVITATIONS_RESEND_SUCCESS, function (GetResponseUserInvitationEvent $event) use ($response): void {
            $event->setResponse($response);
        });
        $controller = new InvitationsController($manager, $this->createStub(UserMailerInterface::class), $dispatcher);

        self::assertSame($response, $controller->resendEmail(42, new Request()));
    }

    public function testAutomaticInvitationFailureIsLogged(): void
    {
        $invitation = $this->createStub(UserInvitationInterface::class);
        $invitation->method('getId')->willReturn(42);
        $exception = new RuntimeException('Transport unavailable');
        $mailer = $this->createMock(UserMailerInterface::class);
        $mailer->expects(self::once())->method('sendInvitationEmail')->willThrowException($exception);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error')->with('Failed to send invitation email.', ['exception' => $exception, 'invitation_id' => 42]);

        (new EmailInvitationListener($mailer, $logger))->onInvitation(new UserInvitationEvent($invitation, new Request()));
    }

    public function testRegistrationEmailFailureIsLogged(): void
    {
        $user = $this->createStub(ConfirmableUser::class);
        $user->method('getUserIdentifier')->willReturn('user-42');
        $manager = $this->createMock(UserManagerInterface::class);
        $manager->expects(self::once())->method('saveEntity')->with($user);
        $exception = new RuntimeException('Transport unavailable');
        $mailer = $this->createMock(UserMailerInterface::class);
        $mailer->expects(self::once())->method('sendRegisterConfirmationEmail')->willThrowException($exception);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error')->with('Failed to send registration confirmation email.', ['exception' => $exception, 'user_identifier' => 'user-42']);

        (new UserRegistrationListener($manager, $mailer, new TokenGenerator(), $logger))->onRegisterSendConfirmationEmail(new GetResponseUserEvent($user, new Request()));
    }

    public function testResetEmailFailureIsLogged(): void
    {
        $user = $this->createStub(PasswordRequestUser::class);
        $user->method('getUserIdentifier')->willReturn('user-42');
        $email = $this->createMock(FormInterface::class);
        $email->expects(self::once())->method('getData')->willReturn('user@example.com');
        $form = $this->createMock(FormInterface::class);
        $form->expects(self::once())->method('get')->with('email')->willReturn($email);
        $manager = $this->createMock(UserManagerInterface::class);
        $manager->expects(self::once())->method('findUserBy')->with(['email' => 'user@example.com'])->willReturn($user);
        $exception = new RuntimeException('Transport unavailable');
        $mailer = $this->createMock(UserMailerInterface::class);
        $mailer->expects(self::once())->method('sendResettingEmail')->willThrowException($exception);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error')->with('Failed to send password reset email.', ['exception' => $exception, 'user_identifier' => 'user-42']);

        (new SendResetPasswordEmailListener($mailer, $manager, new TokenGenerator(), $logger))->sendResetEmail(new GetResponseFormEvent($form, new Request()));
    }

    public function testResendConfirmationFailureIsLogged(): void
    {
        $user = $this->createStub(ConfirmableUser::class);
        $user->method('getUserIdentifier')->willReturn('user-42');
        $exception = new RuntimeException('Transport unavailable');
        $mailer = $this->createMock(UserMailerInterface::class);
        $mailer->expects(self::once())->method('sendRegisterConfirmationEmail')->willThrowException($exception);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error')->with('Failed to resend confirmation email.', ['exception' => $exception, 'user_identifier' => 'user-42']);
        $response = new Response('Mail failed');
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_ERROR, function (GetResponseUserEvent $event) use ($response): void {
            $event->setResponse($response);
        });
        $controller = $this->getMockBuilder(ConfirmationController::class)
            ->setConstructorArgs([$this->createStub(UserManagerInterface::class), $mailer, $dispatcher, $logger])
            ->onlyMethods(['getUser'])
            ->getMock();
        $controller->expects(self::once())->method('getUser')->willReturn($user);

        self::assertSame($response, $controller->resendConfirmation(new Request()));
    }

    public function testRegistrationPersistenceFailureIsNotSwallowed(): void
    {
        // Use a mock that implements both contracts without relying on application entities.
        $user = $this->createStub(ConfirmableUser::class);
        $exception = new RuntimeException('Database unavailable');
        $manager = $this->createMock(UserManagerInterface::class);
        $manager->expects(self::once())->method('saveEntity')->willThrowException($exception);
        $mailer = $this->createMock(UserMailerInterface::class);
        $mailer->expects(self::never())->method('sendRegisterConfirmationEmail');
        $listener = new UserRegistrationListener($manager, $mailer, new TokenGenerator());

        $this->expectExceptionObject($exception);
        $listener->onRegisterSendConfirmationEmail(new GetResponseUserEvent($user, new Request()));
    }
}

interface ConfirmableUser extends UserInterface, ConfirmableInterface
{
}

interface PasswordRequestUser extends UserInterface, PasswordRequestInterface
{
}
