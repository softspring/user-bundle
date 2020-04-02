<?php

namespace Softspring\UserBundle\Mime\Example;

use Softspring\MailerBundle\Mime\Example\ExampleInterface;
use Softspring\MailerBundle\Mime\TranslatableEmail;
use Softspring\UserBundle\Mime\ConfirmationEmail;
use Softspring\UserBundle\Mime\Example\Form\ConfirmationEmailForm;
use Softspring\UserBundle\Mime\Example\Model\ExampleUser;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmationEmailExample implements ExampleInterface
{
    public function getFormType(): string
    {
        return ConfirmationEmailForm::class;
    }

    public function getEmptyData(): array
    {
        return [
            'name' => 'Mery',
            'surname' => 'Jones',
            'username' => 'mery',
            'email' => 'mery@example.com',
        ];
    }

    public function getEmail(array $formData, TranslatorInterface $translator, string $locale): TranslatableEmail
    {
        $user = new ExampleUser();
        $user->setName($formData['name']);
        $user->setSurname($formData['surname']);
        $user->setUsername($formData['username']);
        $user->setEmail($formData['email']);

        $confirmationUrl = '#confirmation-url';

        return new ConfirmationEmail($user, $confirmationUrl, $translator, $locale);
    }
}