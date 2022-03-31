<?php

namespace Softspring\UserBundle\Security\Authorization\Voter;

use Softspring\UserBundle\Model\RolesAdminInterface;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class SwitchUserVoter implements VoterInterface
{
    public function supportsObject($user): bool
    {
        if (!is_object($user)) {
            return false;
        }

        return $user instanceof UserInterface and $user instanceof RolesAdminInterface;
    }

    /**
     * @param UserInterface|RolesAdminInterface $user
     *
     * @return int
     */
    public function vote(TokenInterface $token, $user, array $attributes)
    {
        $role = $attributes[0] ?? '';

        // check role
        if ('ROLE_ALLOWED_TO_SWITCH' !== $role) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!$this->supportsObject($user)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var RolesAdminInterface $administrator */
        $administrator = $token->getUser();

        if (!$administrator instanceof UserInterface) {
            throw new InvalidArgumentException('Invalid user class');
        }

        if ($user->isSuperAdmin()) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($user->isAdmin() && !$administrator->isSuperAdmin()) {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
