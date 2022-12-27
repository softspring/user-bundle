<?php

namespace Softspring\UserBundle\Mime\Example\Model;

use Softspring\UserBundle\Entity\NameSurnameTrait;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\User;
use Softspring\UserBundle\Model\UserIdentifierEmailInterface;
use Softspring\UserBundle\Model\UserIdentifierEmailTrait;

class ExampleUser extends User implements NameSurnameInterface, UserIdentifierEmailInterface
{
    use NameSurnameTrait;
    use UserIdentifierEmailTrait;

    public function getId()
    {
        return $this->getUserIdentifier();
    }

    public function getDisplayName(): ?string
    {
        return $this->getName().' '.$this->getSurname();
    }
}
