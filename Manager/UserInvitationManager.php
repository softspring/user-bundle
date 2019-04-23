<?php

namespace Softspring\UserBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Softspring\User\Manager\UserInvitationManagerInterface;
use Softspring\User\Manager\UserManagerInterface;
use Softspring\User\Model\UserInterface;
use Softspring\User\Model\UserInvitationInterface;

class UserInvitationManager implements UserInvitationManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * UserInvitationManager constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, UserManagerInterface $userManager)
    {
        $this->em = $em;
        $this->userManager = $userManager;
    }

    /**
     * @inheritdoc
     */
    public function getClass(): string
    {
        $metadata = $this->em->getClassMetadata(UserInvitationInterface::class);
        return $metadata->getName();
    }

    /**
     * @inheritdoc
     */
    public function getRepository(): EntityRepository
    {
        return $this->em->getRepository(UserInvitationInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function create(): UserInvitationInterface
    {
        $className = $this->getClass();
        return new $className();
    }

    /**
     * @inheritdoc
     */
    public function createUser(UserInvitationInterface $invitation): UserInterface
    {
        $user = $this->userManager->create();

        $user->setUsername($invitation->getUsername());
        $user->setEmail($invitation->getEmail());
        $user->setName($invitation->getName());
        $user->setSurname($invitation->getSurname());
        $user->setRoles($invitation->getRoles());

        return $user;
    }

    /**
     * @param UserInvitationInterface $userInvitation
     *
     * @throws \Exception
     */
    public function save(UserInvitationInterface $userInvitation): void
    {
        $this->em->persist($userInvitation);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function findInvitationBy(array $criteria): ?UserInvitationInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * @inheritdoc
     */
    public function findInvitationByToken(string $token): ?UserInvitationInterface
    {
        return $this->findInvitationBy(['invitationToken' => $token]);
    }
}