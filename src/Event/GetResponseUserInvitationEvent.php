<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Event;

use Softspring\Component\Events\GetResponseEventInterface;
use Softspring\Component\Events\GetResponseTrait;

class GetResponseUserInvitationEvent extends UserInvitationEvent implements GetResponseEventInterface
{
    use GetResponseTrait;
}
