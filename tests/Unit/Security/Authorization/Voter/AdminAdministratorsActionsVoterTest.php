<?php

namespace Softspring\UserBundle\Tests\Unit\Security\Authorization\Voter;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Security\Authorization\Voter\AdminAdministratorsActionsVoter;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

class AdminAdministratorsActionsVoterTest extends TestCase
{
    public function testAbstainsOnUnsupportedAttribute(): void
    {
        $voter = new AdminAdministratorsActionsVoter();
        $currentUser = $this->createAdministrator();
        $administrator = $this->createAdministrator();

        $vote = $voter->vote($this->createToken($currentUser), $administrator, ['ROLE_ADMIN']);

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $vote);
    }

    public function testDeniesWhenCurrentUserIsNotAdministrator(): void
    {
        $voter = new AdminAdministratorsActionsVoter();
        $currentUser = $this->createUser();
        $administrator = $this->createAdministrator();

        $vote = $voter->vote($this->createToken($currentUser), $administrator, ['PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_UPDATE']);

        self::assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }

    public function testDeniesNonSuperAdminAgainstSuperAdmin(): void
    {
        $voter = new AdminAdministratorsActionsVoter();
        $currentUser = $this->createAdministrator();
        $administrator = $this->createAdministrator(superAdmin: true);

        $vote = $voter->vote($this->createToken($currentUser), $administrator, ['PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_UPDATE']);

        self::assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }

    public function testDeniesDangerousActionsOnOwnAdministrator(): void
    {
        $voter = new AdminAdministratorsActionsVoter();
        $administrator = $this->createAdministrator(superAdmin: true);

        $vote = $voter->vote($this->createToken($administrator), $administrator, ['PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_DELETE']);

        self::assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }

    public function testGrantsSafeActionsOnOwnAdministrator(): void
    {
        $voter = new AdminAdministratorsActionsVoter();
        $administrator = $this->createAdministrator(superAdmin: true);

        $vote = $voter->vote($this->createToken($administrator), $administrator, ['PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_UPDATE']);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $vote);
    }

    public function testThrowsForUnsupportedCurrentUserClass(): void
    {
        $voter = new AdminAdministratorsActionsVoter();
        $administrator = $this->createAdministrator();

        $this->expectException(InvalidArgumentException::class);
        $voter->vote($this->createToken(new class implements SymfonyUserInterface {
            public function getRoles(): array
            {
                return [];
            }

            public function eraseCredentials(): void
            {
            }

            public function getUserIdentifier(): string
            {
                return 'unsupported-user';
            }
        }), $administrator, ['PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_UPDATE']);
    }

    private function createToken(mixed $user): TokenInterface
    {
        return new class($user) implements TokenInterface {
            public function __construct(private readonly ?SymfonyUserInterface $user)
            {
            }

            public function __toString(): string
            {
                return 'test-token';
            }

            public function getUserIdentifier(): string
            {
                return $this->user?->getUserIdentifier() ?? '';
            }

            public function getRoleNames(): array
            {
                return $this->user?->getRoles() ?? [];
            }

            public function getUser(): ?SymfonyUserInterface
            {
                return $this->user;
            }

            public function setUser(SymfonyUserInterface $user): void
            {
                throw new BadMethodCallException('Not implemented for tests.');
            }

            public function eraseCredentials(): void
            {
            }

            public function getAttributes(): array
            {
                return [];
            }

            public function setAttributes(array $attributes): void
            {
            }

            public function hasAttribute(string $name): bool
            {
                return false;
            }

            public function getAttribute(string $name): mixed
            {
                throw new \InvalidArgumentException(sprintf('Attribute "%s" not available.', $name));
            }

            public function setAttribute(string $name, mixed $value): void
            {
            }

            public function __serialize(): array
            {
                return [];
            }

            public function __unserialize(array $data): void
            {
            }
        };
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setEmail(sprintf('user-%s@example.com', uniqid()));

        return $user;
    }

    private function createAdministrator(bool $superAdmin = false): User
    {
        $user = $this->createUser();
        $user->setAdmin(true);
        $user->setSuperAdmin($superAdmin);

        return $user;
    }
}
