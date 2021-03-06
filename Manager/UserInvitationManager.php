<?php

namespace Softspring\UserBundle\Manager;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Softspring\CrudlBundle\Manager\CrudlEntityManagerTrait;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Softspring\UserBundle\Util\TokenGeneratorInterface;

class UserInvitationManager implements UserInvitationManagerInterface
{
    use CrudlEntityManagerTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var TokenGeneratorInterface
     */
    protected $tokenGenerator;

    /**
     * UserInvitationManager constructor.
     *
     * @param EntityManagerInterface  $em
     * @param UserManagerInterface    $userManager
     * @param TokenGeneratorInterface $tokenGenerator
     */
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

    public function createEntity()
    {
        $class = $this->getEntityClass();
        $entity = new $class;

        /** @var UserInvitationInterface $entity */
        $entity->setInvitationToken($this->tokenGenerator->generateToken());

        return $entity;
    }

    /**
     * @deprecated
     */
    public function create(): UserInvitationInterface
    {
        return $this->createEntity();
    }

    /**
     * @inheritdoc
     */
    public function createUser(UserInvitationInterface $invitation): UserInterface
    {
        /** @var UserInterface $user */
        $user = $this->userManager->createEntity();

        $user->setUsername($invitation->getUsername());

        if ($user instanceof UserWithEmailInterface) {
            $user->setEmail($invitation->getEmail());
        }

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
     * @deprecated
     */
    public function save(UserInvitationInterface $userInvitation): void
    {
        $this->saveEntity($userInvitation);
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