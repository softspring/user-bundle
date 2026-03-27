<?php

namespace Softspring\UserBundle\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractWebTestCase extends WebTestCase
{
    protected function setUp(): void
    {
        if ('1' !== ($_SERVER['SFS_USER_TEST_DATABASE_AVAILABLE'] ?? $_ENV['SFS_USER_TEST_DATABASE_AVAILABLE'] ?? null)) {
            $this->markTestSkipped('pdo_sqlite is required to run functional tests in the test application.');
        }
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }

    protected function getUserManager(): UserManagerInterface
    {
        return self::getContainer()->get(UserManagerInterface::class);
    }

    protected function getUserRepository(): object
    {
        return $this->getEntityManager()->getRepository(User::class);
    }

    protected function findUserByEmail(string $email): ?User
    {
        $user = $this->getUserRepository()->findOneBy(['email' => $email]);

        return $user instanceof User ? $user : null;
    }

    protected function createPersistedUser(bool $admin = false, bool $superAdmin = false): User
    {
        $user = new User();
        $user->setName($admin ? 'Admin' : 'Functional');
        $user->setSurname($superAdmin ? 'Super' : 'User');
        $user->setEmail(sprintf('functional-%s@example.com', uniqid()));
        $user->setPlainPassword('123456');
        $user->setLocale('en');
        $user->setAdmin($admin);
        $user->setSuperAdmin($superAdmin);

        $this->getUserManager()->saveEntity($user);

        return $user;
    }
}
