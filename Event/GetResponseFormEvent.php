<?php

namespace Softspring\UserBundle\Event;

use Softspring\ExtraBundle\Event\GetResponseEventInterface;

class GetResponseFormEvent extends FormEvent implements GetResponseEventInterface
{
    use GetResponseTrait;
}