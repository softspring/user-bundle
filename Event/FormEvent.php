<?php

namespace Softspring\UserBundle\Event;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class FormEvent extends Event
{
    /**
     * @var Request|null
     */
    protected $request;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * FormEvent constructor.
     *
     * @param FormInterface $form
     * @param Request|null  $request
     */
    public function __construct(FormInterface $form, ?Request $request = null)
    {
        $this->form = $form;
        $this->request = $request;
    }

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }
}
