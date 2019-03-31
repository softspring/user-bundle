<?php

namespace Softspring\UserBundle\Event;

use Symfony\Component\HttpFoundation\Response;

interface GetResponseEventInterface
{
    /**
     * @return Response|null
     */
    public function getResponse(): ?Response;
}