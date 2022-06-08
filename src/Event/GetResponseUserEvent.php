<?php

namespace Softspring\UserBundle\Event;

use Softspring\Component\Events\GetResponseEventInterface;
use Softspring\Component\Events\GetResponseTrait;

class GetResponseUserEvent extends UserEvent implements GetResponseEventInterface
{
    use GetResponseTrait;
}
