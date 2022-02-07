<?php

namespace Softspring\UserBundle\Mime;

use Softspring\Component\MimeTranslatable\ExampleEmailInterface;
use Softspring\Component\MimeTranslatable\TranslatableEmail;
use Softspring\UserBundle\Mime\Example\Model\ExampleUser;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordEmail extends TranslatableEmail implements ExampleEmailInterface
{
    public static function generateExample(TranslatorInterface $translator, ?string $locale = null): TranslatableEmail
    {
        $user = new ExampleUser();
        $user->setUsername('mery_mccarty');
        $user->setName('Mery');
        $user->setSurname('McCarty');
        $resetUrl = '#reset-url';

        return new self($user, $resetUrl, $translator, $locale);
    }

    /**
     * ConfirmationEmail constructor.
     */
    public function __construct(UserInterface $user, string $resetUrl, TranslatorInterface $translator, ?string $locale = null, Headers $headers = null, AbstractPart $body = null)
    {
        parent::__construct($translator, $locale, $headers, $body);

        $this->setContextParam('user', $user);
        $this->setContextParam('resetUrl', $resetUrl);

        $this->setTranslationParams([
            '%name%' => $user instanceof NameSurnameInterface ? $user->getName() : '',
            '%surname%' => $user instanceof NameSurnameInterface ? $user->getSurname() : '',
            '%username%' => $user->getUsername(),
            '%email%' => $user instanceof UserWithEmailInterface ? $user->getEmail() : '',
            '%reset_url%' => $resetUrl,
        ]);

        $this->htmlTemplate('@SfsUser/reset_password/request.email.twig');

        $this->subject('reset_password.email.subject', 'sfs_user');
    }
}