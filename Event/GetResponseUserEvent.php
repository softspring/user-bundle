<?php

namespace Softspring\UserBundle\Event;

use Softspring\ExtraBundle\Event\GetResponseEventInterface;

class GetResponseUserEvent extends UserEvent implements GetResponseEventInterface
{
    use GetResponseTrait;
}