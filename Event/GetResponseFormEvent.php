<?php

namespace Softspring\UserBundle\Event;

use Softspring\CoreBundle\Event\GetResponseEventInterface;
use Softspring\CoreBundle\Event\GetResponseTrait;

/**
 * Class GetResponseFormEvent
 *
 * @deprecated Use Softspring\CoreBundle\Event\GetResponseFormEvent instead
 */
class GetResponseFormEvent extends FormEvent implements GetResponseEventInterface
{
    use GetResponseTrait;
}