<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use LogicException;
use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Manager\UserInvitationManager;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Softspring\UserBundle\Util\TokenGeneratorInterface;

class UserInvitationManagerTest extends TestCase
{
    public function testCreateEntityAssignsInvitationToken(): void
    {
        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $tokenGenerator->expects($this->once())->method('generateToken')->willReturn('token-123');

        $invitation = $this->createManager(tokenGenerator: $tokenGenerator)->createEntity();

        $this->assertInstanceOf(ExampleInvitation::class, $invitation);
        $this->assertSame('token-123', $invitation->getInvitationToken());
    }

    public function testCreateUserCopiesInvitationData(): void
    {
        $user = new User();
        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('createEntity')->willReturn($user);

        $invitation = new ExampleInvitation();
        $invitation->setEmail('ada@example.com');
        $invitation->setName('Ada');
        $invitation->setSurname('Lovelace');
        $invitation->setSuperAdmin(true);

        $created = $this->createManager(userManager: $userManager)->createUser($invitation);

        $this->assertSame($user, $created);
        $this->assertSame('ada@example.com', $created->getEmail());
        $this->assertSame('Ada', $created->getName());
        $this->assertSame('Lovelace', $created->getSurname());
        $this->assertTrue($created->isAdmin());
        $this->assertTrue($created->isSuperAdmin());
        $this->assertContains('ROLE_ADMIN', $created->getRoles());
        $this->assertContains('ROLE_SUPER_ADMIN', $created->getRoles());
        $this->assertNotNull($created->getConfirmedAt());
    }

    public function testFindInvitationByTokenUsesRepositoryCriteria(): void
    {
        $invitation = new ExampleInvitation();
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['invitationToken' => 'token-123'])
            ->willReturn($invitation);

        $this->assertSame($invitation, $this->createManager(repository: $repository)->findInvitationByToken('token-123'));
    }

    private function createManager(
        ?UserManagerInterface $userManager = null,
        ?TokenGeneratorInterface $tokenGenerator = null,
        ?EntityRepository $repository = null,
    ): TestingUserInvitationManager {
        return new TestingUserInvitationManager(
            $this->createMock(EntityManagerInterface::class),
            $userManager ?? $this->createMock(UserManagerInterface::class),
            $tokenGenerator ?? $this->createMock(TokenGeneratorInterface::class),
            $repository,
        );
    }
}

class TestingUserInvitationManager extends UserInvitationManager
{
    public function __construct(
        EntityManagerInterface $em,
        UserManagerInterface $userManager,
        TokenGeneratorInterface $tokenGenerator,
        private readonly ?EntityRepository $repository = null,
    ) {
        parent::__construct($em, $userManager, $tokenGenerator);
    }

    public function getEntityClass(): string
    {
        return ExampleInvitation::class;
    }

    public function getRepository(): EntityRepository
    {
        return $this->repository ?? throw new LogicException('Repository was not configured');
    }
}
