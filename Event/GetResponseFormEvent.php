<?php

namespace Softspring\UserBundle\Event;

class GetResponseFormEvent extends FormEvent implements GetResponseEventInterface
{
    use GetResponseTrait;
}