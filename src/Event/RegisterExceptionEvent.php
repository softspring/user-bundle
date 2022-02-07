<?php

namespace Softspring\UserBundle\Event;

use Softspring\CoreBundle\Event\GetResponseFormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RegisterExceptionEvent extends GetResponseFormEvent
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var \Exception|null
     */
    protected $throwException;

    public function __construct(FormInterface $form, \Exception $exception, ?Request $request = null)
    {
        parent::__construct($form, $request);
        $this->exception = $exception;
        $this->throwException = $exception;
    }

    public function getException(): \Exception
    {
        return $this->exception;
    }

    public function getThrowException(): ?\Exception
    {
        return $this->throwException;
    }

    public function setThrowException(?\Exception $throwException): void
    {
        $this->throwException = $throwException;
    }
}