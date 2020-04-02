<?php

namespace Softspring\UserBundle\Mime\Example;

use Softspring\MailerBundle\Mime\Example\ExampleInterface;
use Softspring\MailerBundle\Mime\TranslatableEmail;
use Softspring\UserBundle\Mime\Example\Form\InvitationEmailForm;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\Mime\InvitationEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

class InvitationEmailExample implements ExampleInterface
{
    public function getFormType(): string
    {
        return InvitationEmailForm::class;
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
        $invitation = new ExampleInvitation();
        $invitation->setName($formData['name']);
        $invitation->setSurname($formData['surname']);
        $invitation->setUsername($formData['username']);
        $invitation->setEmail($formData['email']);

        $acceptUrl = '#accept-url';

        return new InvitationEmail($invitation, $acceptUrl, $translator, $locale);
    }
}