<?php

namespace Softspring\UserBundle\Mime;

use Softspring\MailerBundle\Mime\TranslatableEmail;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordEmail extends TranslatableEmail
{
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

        $this->htmlTemplate('@SfsUser/reset_password/request-email.html.twig');

        $this->subject('reset_password.request.email.subject', 'sfs_user');
    }
}