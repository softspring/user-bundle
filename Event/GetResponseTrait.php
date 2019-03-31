<?php

namespace Softspring\UserBundle\Event;

use Symfony\Component\HttpFoundation\Response;

trait GetResponseTrait
{
    /**
     * @var Response|null
     */
    protected $response;

    /**
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @param Response|null $response
     */
    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }
}