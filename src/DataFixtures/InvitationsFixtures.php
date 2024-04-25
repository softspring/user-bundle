<?php

namespace Softspring\UserBundle\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;

class InvitationsFixtures extends UserFixtures
{
    protected ?UserInvitationManagerInterface $invitationManager;

    public function __construct(UserManagerInterface $userManager, ?UserInvitationManagerInterface $invitationManager, ?UserAccessManagerInterface $userAccessManager)
    {
        parent::__construct($userManager, $userAccessManager);
        $this->invitationManager = $invitationManager;
    }

    public function load(ObjectManager $manager)
    {
        if (!$this->invitationManager) {
            return;
        }

        $faker = Factory::create('es_ES');

        for ($i = 0; $i < 200; ++$i) {
            $invitation = $this->invitationManager->createEntity();

            if ($invitation instanceof NameSurnameInterface) {
                $invitation->setName($faker->firstName());
                $invitation->setSurname($faker->lastName().' '.$faker->lastName());
            }

            if ($invitation instanceof UserIdentifierUsernameInterface) {
                $invitation->setUsername($faker->unique()->userName());
            }

            $invitation->setEmail($faker->unique()->email());

            if ($faker->boolean(60)) {
                $invitation->setAcceptedAt($faker->dateTimeBetween('-5 years', '-1 minute'));
                $user = $this->createUser($manager);
                $invitation->setUser($user);

                if ($user instanceof ConfirmableInterface) {
                    $user->setConfirmedAt($invitation->getAcceptedAt());
                }

                $manager->persist($user); // $this->userManager->saveEntity($user, false); // this is disabled because wastes a lot of time hashing passwords
            }

            $manager->persist($invitation);
        }

        $manager->flush();
    }
}
