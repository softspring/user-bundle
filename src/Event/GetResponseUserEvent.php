<?php

namespace Softspring\UserBundle\Event;

use Softspring\CoreBundle\Event\GetResponseEventInterface;
use Softspring\CoreBundle\Event\GetResponseTrait;

class GetResponseUserEvent extends UserEvent implements GetResponseEventInterface
{
    use GetResponseTrait;
}
