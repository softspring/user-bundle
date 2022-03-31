<?php

namespace Softspring\UserBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Softspring\Component\CrudlController\Manager\CrudlEntityManagerTrait;
use Softspring\UserBundle\Model\UserAccessInterface;

class UserAccessManager implements UserAccessManagerInterface
{
    use CrudlEntityManagerTrait;

    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getTargetClass(): string
    {
        return UserAccessInterface::class;
    }
}
