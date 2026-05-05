<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Form;

use Symfony\Component\Form\FormTypeInterface;

interface LoginFormInterface extends FormTypeInterface
{
    public function supportsManualLogin(): bool;
}
