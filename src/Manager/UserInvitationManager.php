<?php

namespace Softspring\UserBundle\Manager;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Softspring\CrudlBundle\Manager\CrudlEntityManagerTrait;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\RolesAdminInterface;
use Softspring\UserBundle\Model\RolesInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Softspring\UserBundle\Util\TokenGeneratorInterface;

class UserInvitationManager implements UserInvitationManagerInterface
{
    use CrudlEntityManagerTrait;

    protected EntityManagerInterface $em;

    protected UserManagerInterface $userManager;

    protected TokenGeneratorInterface $tokenGenerator;

    public function __construct(EntityManagerInterface $em, UserManagerInterface $userManager, TokenGeneratorInterface $tokenGenerator)
    {
        $this->em = $em;
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function getTargetClass(): string
    {
        return UserInvitationInterface::class;
    }

    public function createEntity(): object
    {
        $class = $this->getEntityClass();
        $entity = new $class();

        /* @var UserInvitationInterface $entity */
        $entity->setInvitationToken($this->tokenGenerator->generateToken());

        return $entity;
    }

    public function createUser(UserInvitationInterface $invitation): UserInterface
    {
        $user = $this->userManager->createEntity();

        if ($user instanceof UserIdentifierUsernameInterface && $invitation instanceof UserIdentifierUsernameInterface) {
            $user->setUsername($invitation->getUsername());
        }

        if ($user instanceof UserWithEmailInterface) {
            $user->setEmail($invitation->getEmail());
        }

        if ($user instanceof NameSurnameInterface && $invitation instanceof NameSurnameInterface) {
            $user->setName($invitation->getName());
            $user->setSurname($invitation->getSurname());
        }

        if ($invitation instanceof RolesInterface && $user instanceof RolesInterface) {
            $user->setRoles($invitation->getRoles());
        }

        if ($invitation instanceof RolesAdminInterface && $user instanceof RolesAdminInterface) {
            $user->setAdmin($invitation->isAdmin());
            $user->setSuperAdmin($invitation->isSuperAdmin());
        }

        if ($user instanceof ConfirmableInterface) {
            $user->setConfirmedAt(new DateTime('now'));
        }

        return $user;
    }

    public function findInvitationBy(array $criteria): ?UserInvitationInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findInvitationByToken(string $token): ?UserInvitationInterface
    {
        return $this->findInvitationBy(['invitationToken' => $token]);
    }
}
