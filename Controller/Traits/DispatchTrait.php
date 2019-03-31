<?php

namespace Softspring\UserBundle\Controller\Traits;

use Softspring\UserBundle\Event\GetResponseEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

trait DispatchTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param string                    $eventName
     * @param GetResponseEventInterface $event
     *
     * @return null|Response
     */
    protected function dispatchGetResponse(string $eventName, GetResponseEventInterface $event): ?Response
    {
        $this->eventDispatcher->dispatch($eventName, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }

        return null;
    }
}