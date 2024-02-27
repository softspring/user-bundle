<?php

namespace Softspring\UserBundle\Security\Authorization\Voter;

use Softspring\UserBundle\Model\RolesAdminInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class AdminAdministratorsActionsVoter implements VoterInterface
{
    public function supportsObject($administrator): bool
    {
        if (!is_object($administrator)) {
            return false;
        }

        return $administrator instanceof RolesAdminInterface;
    }

    public function vote(TokenInterface $token, $administrator, array $attributes): int
    {
        /** @var RolesAdminInterface $administrator */
        $role = $attributes[0] ?? '';

        // check role
        if (!str_starts_with($role, 'PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_')) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check object
        if (!$this->supportsObject($administrator)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $currentLoggedInUser = $token->getUser();

        if (!$currentLoggedInUser instanceof RolesAdminInterface) {
            throw new InvalidArgumentException('Invalid user class');
        }

        if (!$currentLoggedInUser->isAdmin()) {
            return VoterInterface::ACCESS_DENIED;
        }

        if (!$currentLoggedInUser->isSuperAdmin() && $administrator->isSuperAdmin()) {
            return VoterInterface::ACCESS_DENIED;
        }

        $ownAllowedActions = ['PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_UPDATE', 'PERMISSION_SFS_USER_ADMIN_ADMINISTRATORS_DETAILS'];
        if ($currentLoggedInUser === $administrator && !in_array($role, $ownAllowedActions)) {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
