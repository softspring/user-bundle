<?php

namespace Softspring\UserBundle\Tests\Unit\GoogleIdentityPlatform;

use BadMethodCallException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Softspring\UserBundle\Entity\ConfirmableTrait;
use Softspring\UserBundle\Entity\GoogleIdentityPlatformTrait;
use Softspring\UserBundle\Entity\NameSurnameTrait;
use Softspring\UserBundle\Entity\UserAvatarTrait;
use Softspring\UserBundle\Entity\UserIdentifierEmailTrait;
use Softspring\UserBundle\Entity\UserLastLoginTrait;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformGoogleUser;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformUserSynchronizer;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\GoogleIdentityPlatformAwareInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\User;
use Softspring\UserBundle\Model\UserAvatarInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserIdentifierEmailInterface;
use Softspring\UserBundle\Model\UserLastLoginInterface;

class IdentityPlatformUserSynchronizerTest extends TestCase
{
    public function testSyncCreatesLocalUserForNewIdentityPlatformUser(): void
    {
        $manager = new InMemoryUserManager();
        $synchronizer = new IdentityPlatformUserSynchronizer($manager);

        $result = $synchronizer->sync(new IdentityPlatformGoogleUser(
            identityPlatformUserId: 'gcip-user-1',
            identityProvider: 'google.com',
            identityProviderUserId: 'google-user-1',
            email: 'user@example.com',
            emailVerified: true,
            displayName: 'Jane Doe',
            firstName: 'Jane',
            lastName: 'Doe',
            photoUrl: 'https://example.com/avatar.jpg',
            isNewIdentityPlatformUser: true,
            tenantId: null,
        ));

        self::assertTrue($result->localUserCreated);
        self::assertTrue($result->newIdentityPlatformUser);
        $user = $this->assertTestUser($result->user);
        self::assertSame('user@example.com', $user->getEmail());
        self::assertSame('Jane', $user->getName());
        self::assertSame('Doe', $user->getSurname());
        self::assertSame('gcip-user-1', $user->getIdentityPlatformUserId());
        self::assertSame('google.com', $user->getIdentityProvider());
        self::assertSame('google-user-1', $user->getIdentityProviderUserId());
        self::assertSame('identity_platform', $user->getAuthSource());
        self::assertNotNull($user->getLastLogin());
        self::assertSame('https://example.com/avatar.jpg', $user->getAvatarUrl());
    }

    public function testSyncReusesExistingUserByEmail(): void
    {
        $manager = new InMemoryUserManager();
        $existingUser = new TestUser();
        $existingUser->setEmail('user@example.com');
        $manager->saveEntity($existingUser);

        $synchronizer = new IdentityPlatformUserSynchronizer($manager);

        $result = $synchronizer->sync(new IdentityPlatformGoogleUser(
            identityPlatformUserId: 'gcip-user-2',
            identityProvider: 'google.com',
            identityProviderUserId: 'google-user-2',
            email: 'user@example.com',
            emailVerified: false,
            displayName: 'Existing User',
            firstName: 'Existing',
            lastName: 'User',
            photoUrl: null,
            isNewIdentityPlatformUser: false,
            tenantId: null,
        ));

        self::assertFalse($result->localUserCreated);
        self::assertSame($existingUser, $result->user);
        self::assertSame('gcip-user-2', $this->assertTestUser($result->user)->getIdentityPlatformUserId());
    }

    private function assertTestUser(UserInterface $user): TestUser
    {
        self::assertInstanceOf(TestUser::class, $user);

        return $user;
    }
}

class InMemoryUserManager implements UserManagerInterface
{
    /**
     * @var UserInterface[]
     */
    private array $users = [];

    public function getTargetClass(): string
    {
        return TestUser::class;
    }

    public function getEntityClass(): string
    {
        return TestUser::class;
    }

    public function getEntityClassReflection(): ReflectionClass
    {
        return new ReflectionClass(TestUser::class);
    }

    public function getRepository(): EntityRepository
    {
        throw new BadMethodCallException('Not needed in this test.');
    }

    public function getEntityManager(): EntityManagerInterface
    {
        throw new BadMethodCallException('Not needed in this test.');
    }

    public function createEntity(): object
    {
        return new TestUser();
    }

    public function saveEntity(object $entity): void
    {
        $this->users[spl_object_hash($entity)] = $entity;
    }

    public function deleteEntity(object $entity): void
    {
        unset($this->users[spl_object_hash($entity)]);
    }

    public function findUserBy(array $criteria): ?UserInterface
    {
        foreach ($this->users as $user) {
            $matched = true;

            foreach ($criteria as $field => $value) {
                $getter = 'get'.ucfirst($field);

                if (!method_exists($user, $getter) || $user->$getter() !== $value) {
                    $matched = false;
                    break;
                }
            }

            if ($matched) {
                return $user;
            }
        }

        return null;
    }

    public function findUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->findUserBy(['email' => $identifier]);
    }

    public function findUserByConfirmationToken(string $token): ?ConfirmableInterface
    {
        return null;
    }
}

class TestUser extends User implements UserIdentifierEmailInterface, NameSurnameInterface, UserLastLoginInterface, ConfirmableInterface, GoogleIdentityPlatformAwareInterface, UserAvatarInterface
{
    use ConfirmableTrait;
    use GoogleIdentityPlatformTrait;
    use NameSurnameTrait;
    use UserAvatarTrait;
    use UserIdentifierEmailTrait;
    use UserLastLoginTrait;

    protected ?string $id = 'test-user';

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDisplayName(): string
    {
        return trim(implode(' ', array_filter([$this->getName(), $this->getSurname()]))) ?: ($this->getEmail() ?? 'test-user');
    }

    public function eraseCredentials(): void
    {
    }
}
