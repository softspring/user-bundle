<?php

namespace Softspring\UserBundle\Event;

use Softspring\ExtraBundle\Event\GetResponseEventInterface;

/**
 * Class GetResponseFormEvent
 *
 * @deprecated Use Softspring\CoreBundle\Event\GetResponseFormEvent instead
 */
class GetResponseFormEvent extends FormEvent implements GetResponseEventInterface
{
    use GetResponseTrait;
}