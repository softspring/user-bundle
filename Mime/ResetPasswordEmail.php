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

class ResetPasswordEmail extends TranslatableEmail
{
    public static function generateExample(TranslatorInterface $translator, ?string $locale = null): TranslatableEmail
    {
        $user = new ExampleUser();
        $user->setName('Mery');
        $user->setSurname('McCarty');
        $resetUrl = '#reset-url';
        return new self($user, $resetUrl, $translator, $locale);
    }

    /**
     * ConfirmationEmail constructor.
     *
     * @param UserInterface       $user
     * @param string              $resetUrl
     * @param TranslatorInterface $translator
     * @param string|null         $locale
     * @param Headers|null        $headers
     * @param AbstractPart|null   $body
     */
    public function __construct(UserInterface $user, string $resetUrl, TranslatorInterface $translator, ?string $locale = null,  Headers $headers = null, AbstractPart $body = null)
    {
        parent::__construct($translator, $locale, $headers, $body);

        $this->context['user'] = $user;
        $this->context['resetUrl'] = $resetUrl;

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