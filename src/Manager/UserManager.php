<?php

namespace Softspring\UserBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Softspring\CrudlBundle\Manager\CrudlEntityManagerTrait;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserPasswordInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager implements UserManagerInterface
{
    use CrudlEntityManagerTrait;
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * UserManager constructor.
     */
    public function __construct(EntityManagerInterface $em, EncoderFactoryInterface $encoderFactory)
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
            $encoder = $this->encoderFactory->getEncoder($user);
        } catch (RuntimeException $e) {
            $encoder = $this->encoderFactory->getEncoder(UserInterface::class);
        }

        if ($encoder instanceof BCryptPasswordEncoder) {
            $user->setSalt(null);
        } else {
            $user->setSalt(rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '='));
        }

        $user->setPassword($encoder->encodePassword($plainPassword, $user->getSalt()));
        $user->eraseCredentials();
    }
}
