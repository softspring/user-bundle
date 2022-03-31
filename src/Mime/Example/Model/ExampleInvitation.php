<?php

namespace Softspring\UserBundle\Mime\Example\Model;

use Softspring\Component\DoctrineTemplates\Entity\Traits\UniqIdString;
use Softspring\UserBundle\Entity\NameSurnameTrait;
use Softspring\UserBundle\Entity\RolesAdminTrait;
use Softspring\UserBundle\Entity\UserIdentifierEmailTrait;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\RolesAdminInterface;
use Softspring\UserBundle\Model\UserIdentifierEmailInterface;
use Softspring\UserBundle\Model\UserInvitation;

class ExampleInvitation extends UserInvitation implements RolesAdminInterface, UserIdentifierEmailInterface, NameSurnameInterface
{
    use UniqIdString;
    use RolesAdminTrait;
    use NameSurnameTrait;
    use UserIdentifierEmailTrait;
}
