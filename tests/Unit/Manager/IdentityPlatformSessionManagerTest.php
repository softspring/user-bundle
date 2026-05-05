<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Manager;

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
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformGoogleAuthenticator;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformUserSynchronizer;
use Softspring\UserBundle\GoogleIdentityPlatform\LocalUserAlreadyExistsException;
use Softspring\UserBundle\GoogleIdentityPlatform\LocalUserNotFoundException;
use Softspring\UserBundle\Manager\IdentityPlatformSessionManager;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\GoogleIdentityPlatformAwareInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\User;
use Softspring\UserBundle\Model\UserAvatarInterface;
use Softspring\UserBundle\Model\UserIdentifierEmailInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserLastLoginInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class IdentityPlatformSessionManagerTest extends TestCase
{
    public function testAuthenticateGoogleCredentialReturnsLocalUserAndTokens(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(json_encode([
                'localId' => 'gcip-user-1',
                'providerId' => 'google.com',
                'federatedId' => 'google-user-1',
                'email' => 'user@example.com',
                'emailVerified' => true,
                'displayName' => 'Jane Doe',
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'photoUrl' => 'https://example.com/avatar.jpg',
                'isNewUser' => true,
                'idToken' => 'identity-platform-id-token',
                'refreshToken' => 'identity-platform-refresh-token',
                'expiresIn' => '3600',
            ], JSON_THROW_ON_ERROR)),
        ]);

        $userManager = new InMemoryIdentityPlatformUserManager();
        $manager = new IdentityPlatformSessionManager(
            $httpClient,
            new IdentityPlatformGoogleAuthenticator($httpClient, 'api-key', null),
            new IdentityPlatformUserSynchronizer($userManager),
            $userManager,
            'api-key',
            null,
        );

        $result = $manager->authenticateGoogleCredential('https://example.test/api/app/auth/google', 'google-credential');

        self::assertSame('identity-platform-id-token', $result->tokens->accessToken);
        self::assertSame('identity-platform-refresh-token', $result->tokens->refreshToken);
        self::assertSame(3600, $result->tokens->expiresIn);
        self::assertTrue($result->syncResult->localUserCreated);
        self::assertSame('gcip-user-1', $this->assertTestUser($result->syncResult->user)->getIdentityPlatformUserId());
    }

    public function testLoadUserFromIdTokenReusesExistingLocalUser(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(json_encode([
                'users' => [[
                    'localId' => 'gcip-user-2',
                    'email' => 'existing@example.com',
                    'emailVerified' => true,
                    'displayName' => 'Existing User',
                    'photoUrl' => 'https://example.com/existing.jpg',
                    'providerUserInfo' => [[
                        'providerId' => 'google.com',
                        'federatedId' => 'google-user-2',
                    ]],
                ]],
            ], JSON_THROW_ON_ERROR)),
        ]);

        $userManager = new InMemoryIdentityPlatformUserManager();
        $existingUser = new IdentityPlatformTestUser();
        $existingUser->setEmail('existing@example.com');
        $existingUser->setIdentityPlatformUserId('gcip-user-2');
        $userManager->saveEntity($existingUser);

        $manager = new IdentityPlatformSessionManager(
            $httpClient,
            new IdentityPlatformGoogleAuthenticator($httpClient, 'api-key', null),
            new IdentityPlatformUserSynchronizer($userManager),
            $userManager,
            'api-key',
            null,
        );

        self::assertSame($existingUser, $manager->loadUserFromIdToken('identity-platform-id-token'));
    }

    public function testRefreshTokensMapsSecureTokenResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(json_encode([
                'id_token' => 'next-id-token',
                'refresh_token' => 'next-refresh-token',
                'expires_in' => '1800',
            ], JSON_THROW_ON_ERROR)),
        ]);

        $userManager = new InMemoryIdentityPlatformUserManager();
        $manager = new IdentityPlatformSessionManager(
            $httpClient,
            new IdentityPlatformGoogleAuthenticator($httpClient, 'api-key', null),
            new IdentityPlatformUserSynchronizer($userManager),
            $userManager,
            'api-key',
            null,
        );

        $tokens = $manager->refreshTokens('refresh-token');

        self::assertSame('next-id-token', $tokens->accessToken);
        self::assertSame('next-refresh-token', $tokens->refreshToken);
        self::assertSame(1800, $tokens->expiresIn);
    }

    public function testLoginWithGoogleCredentialFailsWhenNoLocalUserExists(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(json_encode([
                'localId' => 'gcip-user-3',
                'providerId' => 'google.com',
                'federatedId' => 'google-user-3',
                'email' => 'missing@example.com',
                'emailVerified' => true,
                'displayName' => 'Missing User',
                'idToken' => 'identity-platform-id-token',
                'refreshToken' => 'identity-platform-refresh-token',
                'expiresIn' => '3600',
            ], JSON_THROW_ON_ERROR)),
        ]);

        $userManager = new InMemoryIdentityPlatformUserManager();
        $manager = new IdentityPlatformSessionManager(
            $httpClient,
            new IdentityPlatformGoogleAuthenticator($httpClient, 'api-key', null),
            new IdentityPlatformUserSynchronizer($userManager),
            $userManager,
            'api-key',
            null,
        );

        $this->expectException(LocalUserNotFoundException::class);
        $this->expectExceptionMessage('No local account matches this Google account. Use registration first.');

        $manager->loginWithGoogleCredential('https://example.test/api/app/login/google', 'google-credential');
    }

    public function testRegisterWithGoogleCredentialFailsWhenLocalUserExists(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(json_encode([
                'localId' => 'gcip-user-4',
                'providerId' => 'google.com',
                'federatedId' => 'google-user-4',
                'email' => 'existing@example.com',
                'emailVerified' => true,
                'displayName' => 'Existing User',
                'idToken' => 'identity-platform-id-token',
                'refreshToken' => 'identity-platform-refresh-token',
                'expiresIn' => '3600',
            ], JSON_THROW_ON_ERROR)),
        ]);

        $userManager = new InMemoryIdentityPlatformUserManager();
        $existingUser = new IdentityPlatformTestUser();
        $existingUser->setEmail('existing@example.com');
        $userManager->saveEntity($existingUser);

        $manager = new IdentityPlatformSessionManager(
            $httpClient,
            new IdentityPlatformGoogleAuthenticator($httpClient, 'api-key', null),
            new IdentityPlatformUserSynchronizer($userManager),
            $userManager,
            'api-key',
            null,
        );

        $this->expectException(LocalUserAlreadyExistsException::class);
        $this->expectExceptionMessage('A local account already exists for this Google account. Use sign in instead.');

        $manager->registerWithGoogleCredential('https://example.test/api/app/register/google', 'google-credential');
    }

    private function assertTestUser(UserInterface $user): IdentityPlatformTestUser
    {
        self::assertInstanceOf(IdentityPlatformTestUser::class, $user);

        return $user;
    }
}

class InMemoryIdentityPlatformUserManager implements UserManagerInterface
{
    /**
     * @var UserInterface[]
     */
    private array $users = [];

    public function getTargetClass(): string
    {
        return IdentityPlatformTestUser::class;
    }

    public function getEntityClass(): string
    {
        return IdentityPlatformTestUser::class;
    }

    public function getEntityClassReflection(): ReflectionClass
    {
        return new ReflectionClass(IdentityPlatformTestUser::class);
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
        return new IdentityPlatformTestUser();
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

class IdentityPlatformTestUser extends User implements UserIdentifierEmailInterface, NameSurnameInterface, UserLastLoginInterface, ConfirmableInterface, GoogleIdentityPlatformAwareInterface, UserAvatarInterface
{
    use ConfirmableTrait;
    use GoogleIdentityPlatformTrait;
    use NameSurnameTrait;
    use UserAvatarTrait;
    use UserIdentifierEmailTrait;
    use UserLastLoginTrait;

    protected ?string $id = 'identity-platform-test-user';

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDisplayName(): string
    {
        return trim(implode(' ', array_filter([$this->getName(), $this->getSurname()]))) ?: ($this->getEmail() ?? 'identity-platform-test-user');
    }

    public function eraseCredentials(): void
    {
    }
}
