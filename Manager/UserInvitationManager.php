<?php

namespace Softspring\UserBundle\Manager;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;

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
     * @param EntityManagerInterface $em
     * @param UserManagerInterface $userManager
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
        /** @var EntityRepository $repo */
        $repo = $this->em->getRepository(UserInvitationInterface::class);

        return $repo;
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
        /** @var UserInterface $user */
        $user = $this->userManager->createEntity();

        $user->setUsername($invitation->getUsername());
        $user->setEmail($invitation->getEmail());

        if ($user instanceof NameSurnameInterface) {
            $user->setName($invitation->getName());
            $user->setSurname($invitation->getSurname());
        }

        $user->setRoles($invitation->getRoles());

        if ($user instanceof ConfirmableInterface) {
            $user->setConfirmedAt(new DateTime('now'));
        }

        return $user;
    }

    /**
     * @param UserInvitationInterface $userInvitation
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
        /** @var UserInvitationInterface|null $invitation */
        $invitation = $this->getRepository()->findOneBy($criteria);

        return $invitation;
    }

    /**
     * @inheritdoc
     */
    public function findInvitationByToken(string $token): ?UserInvitationInterface
    {
        return $this->findInvitationBy(['invitationToken' => $token]);
    }
}