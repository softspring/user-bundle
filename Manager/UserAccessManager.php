<?php

namespace Softspring\UserBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Softspring\UserBundle\Model\UserAccessInterface;

class UserAccessManager implements UserAccessManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * UserAccessManager constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function getClass(): string
    {
        $metadata = $this->em->getClassMetadata(UserAccessInterface::class);
        return $metadata->getName();
    }

    /**
     * @return EntityRepository
     */
    public function getRepository(): EntityRepository
    {
        /** @var EntityRepository $repo */
        $repo = $this->em->getRepository(UserAccessInterface::class);

        return $repo;
    }

    /**
     * @inheritdoc
     */
    public function create(): UserAccessInterface
    {
        $className = $this->getClass();
        return new $className();
    }

    /**
     * @param UserAccessInterface $userAccess
     */
    public function save(UserAccessInterface $userAccess): void
    {
        $this->em->persist($userAccess);
        $this->em->flush();
    }
}