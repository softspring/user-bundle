<?php

namespace Softspring\UserBundle\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Softspring\UserBundle\Model\RolesAdminInterface;

class AdministratorsFixtures extends UserFixtures
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; ++$i) {
            $user = $this->createUser($manager);

            if ($user instanceof RolesAdminInterface) {
                $user->setAdmin(true);
                if (rand(0, 100) >= 90) {
                    $user->setSuperAdmin(true);
                }
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['sfs_user_admins'];
    }
}
