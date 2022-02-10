<?php

namespace Softspring\UserBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Softspring\CrudlBundle\Manager\CrudlEntityManagerTrait;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserPasswordInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;

class UserManager implements UserManagerInterface
{
    use CrudlEntityManagerTrait;

    protected EntityManagerInterface $em;

    protected PasswordHasherFactoryInterface $encoderFactory;

    /**
     * UserManager constructor.
     */
    public function __construct(EntityManagerInterface $em, PasswordHasherFactoryInterface $encoderFactory)
    {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
    }

    public function getTargetClass(): string
    {
        return UserInterface::class;
    }

    /**
     * @param UserInterface $user
     *
     * @throws Exception
     */
    public function saveEntity($user): void
    {
        if (!$this->getEntityClassReflection()->isInstance($user)) {
            throw new \InvalidArgumentException(sprintf('$user must be an instance of %s', $this->getEntityClass()));
        }

        $this->hashPassword($user);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserBy(array $criteria): ?UserInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->getRepository()->findOneBy($criteria);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsername(string $username): ?UserInterface
    {
        return $this->findUserBy(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->findUserBy(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface
    {
        if ($this->getEntityClassReflection()->implementsInterface(UserWithEmailInterface::class) && preg_match('/^.+@\S+\.\S+$/', $usernameOrEmail)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * @param string $token
     *
     * @return UserInterface|null
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(['confirmationToken' => $token]);
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

        if ($user instanceof LegacyPasswordAuthenticatedUserInterface && $user instanceof UserPasswordInterface) {
            $user->setSalt(rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '='));
        }

        $user->setPassword($hasher->hash($plainPassword, $user->getSalt()));
        $user->eraseCredentials();
    }
}
