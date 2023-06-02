<?php

namespace Softspring\UserBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserAccessInterface;
use Softspring\UserBundle\Model\UserAccessLocationInterface;
use Softspring\UserBundle\Model\UserHasLocalePreferenceInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserLastLoginInterface;
use Softspring\UserBundle\Model\UserPasswordInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    protected UserManagerInterface $userManager;
    protected ?UserAccessManagerInterface $userAccessManager;

    public function __construct(UserManagerInterface $userManager, UserAccessManagerInterface $userAccessManager = null)
    {
        $this->userManager = $userManager;
        $this->userAccessManager = $userAccessManager;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 300; ++$i) {
            $this->createUser($manager);
        }

        $manager->flush();
    }

    protected function createUser(ObjectManager $manager): UserInterface
    {
        $faker = \Faker\Factory::create('es_ES');

        /** @var UserInterface $user */
        $user = $this->userManager->createEntity();

        if ($user instanceof NameSurnameInterface) {
            $user->setName($faker->firstName());
            $user->setSurname($faker->lastName().' '.$faker->lastName());
        }

        if ($user instanceof UserIdentifierUsernameInterface) {
            $user->setUsername($faker->userName());
        }

        if ($user instanceof UserWithEmailInterface) {
            $user->setEmail($faker->unique()->email());
        }

        if ($user instanceof UserPasswordInterface) {
            $user->setPlainPassword('123546');
        }

        if ($user instanceof ConfirmableInterface) {
            $user->setConfirmedAt($faker->boolean(70) ? $faker->dateTimeBetween('-5 years', '-1 minute') : null);
        }

        if ($user instanceof UserLastLoginInterface) {
            if ($user instanceof ConfirmableInterface && !$user->getConfirmedAt()) {
                /* @var UserLastLoginInterface $user */
                $user->setLastLogin(null);
            } else {
                $user->setLastLogin($faker->boolean(95) ? $faker->dateTimeBetween('-2 months', '-1 minute') : null);
            }
        }

        if ($user instanceof UserHasLocalePreferenceInterface) {
            $user->setLocale($faker->boolean(60) ? $faker->languageCode() : null);
        }

        $manager->persist($user); // $this->userManager->saveEntity($user, false); // this is disabled because wastes a lot of time hashing passwords

        if ($this->userAccessManager) {
            if ($user instanceof ConfirmableInterface && !$user->getConfirmedAt()) {
                return $user;
            }

            if ($user instanceof UserLastLoginInterface && $user->getLastLogin()) {
                $this->createUserAccess($manager, $user);
            }

            for ($j = 0; $j < rand(0, 10); ++$j) {
                $access = $this->createUserAccess($manager, $user);
                $access->setLoginAt($faker->dateTimeBetween('-5 years', '-1 minute'));
            }
        }

        return $user;
    }

    protected function createUserAccess(ObjectManager $manager, object $user): UserAccessInterface
    {
        $faker = \Faker\Factory::create('es_ES');

        $access = $this->userAccessManager->createEntity();
        $access->setUser($user);
        $access->setUserAgent($faker->userAgent());
        $access->setIp($faker->ipv4());
        $access->setLoginAt($user instanceof UserLastLoginInterface ? $user->getLastLogin() : new \DateTime('now'));

        if ($access instanceof UserAccessLocationInterface) {
            $access->setCountry($faker->countryCode());
            $access->setRegion($faker->countryCode());
            $access->setCity($faker->city());
        }

        $manager->persist($access);

        return $access;
    }

    public static function getGroups(): array
    {
        return ['sfs_user'];
    }
}
