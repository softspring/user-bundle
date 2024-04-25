<?php

namespace Softspring\UserBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Softspring\Component\CrudlController\Manager\CrudlEntityManagerTrait;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\UserIdentifierEmailInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserPasswordInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;

class UserManager implements UserManagerInterface
{
    use CrudlEntityManagerTrait;

    protected EntityManagerInterface $em;

    protected PasswordHasherFactoryInterface $encoderFactory;

    public function __construct(EntityManagerInterface $em, PasswordHasherFactoryInterface $encoderFactory)
    {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
    }

    public function getTargetClass(): string
    {
        return UserInterface::class;
    }

    public function saveEntity(object $entity, bool $flush = true): void
    {
        if (!$this->getEntityClassReflection()->isInstance($entity)) {
            throw new InvalidArgumentException(sprintf('$entity must be an instance of %s', $this->getEntityClass()));
        }

        /* @var UserInterface $entity */
        $this->hashPassword($entity);

        $this->em->persist($entity);
        $flush && $this->em->flush();
    }

    public function findUserBy(array $criteria): ?UserInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findUserByIdentifier(string $identifier): ?UserInterface
    {
        if ($this->getEntityClassReflection()->implementsInterface(UserIdentifierUsernameInterface::class)) {
            return $this->findUserBy(['username' => $identifier]);
        }

        if ($this->getEntityClassReflection()->implementsInterface(UserIdentifierEmailInterface::class)) {
            return $this->findUserBy(['email' => $identifier]);
        }

        throw new Exception('Unknown user identifier field');
    }

    public function findUserByConfirmationToken(string $token): ?ConfirmableInterface
    {
        /** @var ?ConfirmableInterface $user */
        $user = $this->findUserBy(['confirmationToken' => $token]);

        return $user;
    }

    /**
     * @throws Exception
     */
    protected function hashPassword(UserInterface $user): void
    {
        if (!$user instanceof UserPasswordInterface) {
            return;
        }

        if (!$plainPassword = $user->getPlainPassword()) {
            return;
        }

        try {
            $hasher = $this->encoderFactory->getPasswordHasher($user);
        } catch (RuntimeException $e) {
            $hasher = $this->encoderFactory->getPasswordHasher(UserInterface::class);
        }

        if ($user instanceof LegacyPasswordAuthenticatedUserInterface) {
            /* @var UserPasswordInterface $user */
            $user->setSalt(rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '='));
            /* @var LegacyPasswordHasherInterface $hasher */
            $user->setPassword($hasher->hash($plainPassword, $user->getSalt()));
        } else {
            $user->setPassword($hasher->hash($plainPassword));
        }

        $user->eraseCredentials();
    }
}
