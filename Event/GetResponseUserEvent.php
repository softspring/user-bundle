<?php

namespace Softspring\UserBundle\Event;

class GetResponseUserEvent extends UserEvent implements GetResponseEventInterface
{
    use GetResponseTrait;
}