<?php

namespace Softspring\UserBundle\Mime\Example\Model;

use Softspring\UserBundle\Entity\EmailTrait;
use Softspring\UserBundle\Entity\NameSurnameTrait;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\User;
use Softspring\UserBundle\Model\UserWithEmailInterface;

class ExampleUser extends User implements NameSurnameInterface, UserWithEmailInterface
{
    use NameSurnameTrait;
    use EmailTrait;

    public function getId()
    {
        // TODO: Implement getId() method.
    }
}
