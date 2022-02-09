<?php

namespace Softspring\UserBundle\Security\Authorization\Voter;

use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class AdminAdministratorsActionsVoter implements VoterInterface
{
    /**
     * @param mixed $administrator
     *
     * @return bool
     */
    public function supportsObject($administrator)
    {
        if (!is_object($administrator)) {
            return false;
        }

        return $administrator instanceof UserInterface;
    }

    /**
     * @param UserInterface $administrator
     *
     * @return int
     */
    public function vote(TokenInterface $token, $administrator, array $attributes)
    {
        $role = $attributes[0] ?? '';

        // check role
        if ('ROLE_ADMIN_ADMINISTRATORS_' !== substr($role, 0, 26)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check object
        if (!$this->supportsObject($administrator)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $currentLoggedInUser = $token->getUser();

        if (!$currentLoggedInUser instanceof UserInterface) {
            throw new InvalidArgumentException('Invalid user class');
        }

        if (!$currentLoggedInUser->isAdmin()) {
            return VoterInterface::ACCESS_DENIED;
        }

        if (!$currentLoggedInUser->isSuperAdmin() && $administrator->isSuperAdmin()) {
            return VoterInterface::ACCESS_DENIED;
        }

        $ownAllowedActions = ['ROLE_ADMIN_ADMINISTRATORS_UPDATE', 'ROLE_ADMIN_ADMINISTRATORS_DETAILS'];
        if ($currentLoggedInUser == $administrator && !in_array($role, $ownAllowedActions)) {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
