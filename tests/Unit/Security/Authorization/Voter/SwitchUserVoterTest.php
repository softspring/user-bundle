<?php

namespace Softspring\UserBundle\Tests\Unit\Security\Authorization\Voter;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Security\Authorization\Voter\SwitchUserVoter;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

class SwitchUserVoterTest extends TestCase
{
    public function testAbstainsOnUnsupportedAttribute(): void
    {
        $voter = new SwitchUserVoter();

        $vote = $voter->vote($this->createToken($this->createAdministrator()), $this->createUser(), ['ROLE_USER']);

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $vote);
    }

    public function testDeniesSwitchingToSuperAdmin(): void
    {
        $voter = new SwitchUserVoter();

        $vote = $voter->vote($this->createToken($this->createAdministrator(superAdmin: true)), $this->createAdministrator(superAdmin: true), ['ROLE_ALLOWED_TO_SWITCH']);

        self::assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }

    public function testDeniesSwitchingToAdministratorForNonSuperAdmin(): void
    {
        $voter = new SwitchUserVoter();

        $vote = $voter->vote($this->createToken($this->createAdministrator()), $this->createAdministrator(), ['ROLE_ALLOWED_TO_SWITCH']);

        self::assertSame(VoterInterface::ACCESS_DENIED, $vote);
    }

    public function testGrantsSwitchingToRegularUser(): void
    {
        $voter = new SwitchUserVoter();

        $vote = $voter->vote($this->createToken($this->createAdministrator()), $this->createUser(), ['ROLE_ALLOWED_TO_SWITCH']);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $vote);
    }

    public function testGrantsSwitchingToAdministratorForSuperAdmin(): void
    {
        $voter = new SwitchUserVoter();

        $vote = $voter->vote($this->createToken($this->createAdministrator(superAdmin: true)), $this->createAdministrator(), ['ROLE_ALLOWED_TO_SWITCH']);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $vote);
    }

    public function testThrowsForUnsupportedCurrentUserClass(): void
    {
        $voter = new SwitchUserVoter();

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
        }), $this->createUser(), ['ROLE_ALLOWED_TO_SWITCH']);
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
