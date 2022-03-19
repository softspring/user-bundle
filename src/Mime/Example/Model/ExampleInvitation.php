<?php

namespace Softspring\UserBundle\Mime\Example\Model;

use Softspring\UserBundle\Entity\NameSurnameTrait;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInvitation;

class ExampleInvitation extends UserInvitation implements NameSurnameInterface
{
    use NameSurnameTrait;

    public function getId()
    {
        // TODO: Implement getId() method.
    }
}
