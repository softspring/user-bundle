<?php

namespace Softspring\UserBundle\Tests\TestApplication\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        protected UserManagerInterface $userManager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setName('Regular');
        $user->setSurname('User');
        $user->setEmail('user@example.com');
        $user->setPlainPassword('123456');
        $user->setLocale('en');
        $this->userManager->saveEntity($user);

        $admin = new User();
        $admin->setName('Admin');
        $admin->setSurname('User');
        $admin->setEmail('admin@example.com');
        $admin->setPlainPassword('123456');
        $admin->setAdmin(true);
        $admin->setLocale('en');
        $this->userManager->saveEntity($admin);
    }

    public static function getGroups(): array
    {
        return ['test_application'];
    }
}
