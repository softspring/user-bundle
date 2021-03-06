<?php

namespace Softspring\UserBundle\Mime;

use Softspring\MailerBundle\Mime\TranslatableEmail;
use Softspring\UserBundle\Mime\Example\Model\ExampleUser;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmationEmail extends TranslatableEmail
{
    public static function generateExample(TranslatorInterface $translator, ?string $locale = null): TranslatableEmail
    {
        $user = new ExampleUser();
        $user->setName('Mery');
        $user->setSurname('McCarty');
        $confirmationUrl = '#confirm-url';
        return new self($user, $confirmationUrl, $translator, $locale);
    }

    /**
     * ConfirmationEmail constructor.
     *
     * @param UserInterface       $user
     * @param string              $confirmationUrl
     * @param TranslatorInterface $translator
     * @param string|null         $locale
     * @param Headers|null        $headers
     * @param AbstractPart|null   $body
     */
    public function __construct(UserInterface $user, string $confirmationUrl, TranslatorInterface $translator, ?string $locale = null,  Headers $headers = null, AbstractPart $body = null)
    {
        parent::__construct($translator, $locale, $headers, $body);

        $this->setContextParam('user', $user);
        $this->setContextParam('confirmationUrl', $confirmationUrl);

        $this->setTranslationParams([
            '%name%' => $user instanceof NameSurnameInterface ? $user->getName() : '',
            '%surname%' => $user instanceof NameSurnameInterface ? $user->getSurname() : '',
            '%username%' => $user->getUsername(),
            '%email%' => $user instanceof UserWithEmailInterface ? $user->getEmail() : '',
            '%confirmation_url%' => $confirmationUrl,
        ]);

        $this->htmlTemplate('@SfsUser/register/confirmation.email.twig');

        $this->subject('register.confirm.email.subject', 'sfs_user');
    }
}