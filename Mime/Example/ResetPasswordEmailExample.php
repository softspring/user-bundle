<?php

namespace Softspring\UserBundle\Mime\Example;

use Softspring\MailerBundle\Mime\ExampleInterface;
use Softspring\MailerBundle\Mime\TranslatableEmail;
use Softspring\UserBundle\Mime\Example\Form\ResetPasswordEmailForm;
use Softspring\UserBundle\Mime\Example\Model\ExampleUser;
use Softspring\UserBundle\Mime\ResetPasswordEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordEmailExample implements ExampleInterface
{
    public function getFormType(): string
    {
        return ResetPasswordEmailForm::class;
    }

    public function getEmptyData(): array
    {
        return [
            'name' => 'Juan',
            'surname' => 'García',
        ];
    }

    public function getEmail(array $formData, TranslatorInterface $translator, string $locale): TranslatableEmail
    {
        $user = new ExampleUser();
        $user->setName($formData['name']);
        $user->setSurname($formData['surname']);

        $resetUrl = '#reset-url';

        return new ResetPasswordEmail($user, $resetUrl, $translator, $locale);
    }
}