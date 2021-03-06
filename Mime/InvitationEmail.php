<?php

namespace Softspring\UserBundle\Mime;

use Softspring\MailerBundle\Mime\TranslatableEmail;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Contracts\Translation\TranslatorInterface;

class InvitationEmail extends TranslatableEmail
{
    public static function generateExample(TranslatorInterface $translator, ?string $locale = null): TranslatableEmail
    {
        $user = new ExampleInvitation();
        $user->setName('Mery');
        $user->setSurname('McCarty');
        $acceptUrl = '#accept-url';
        return new self($user, $acceptUrl, $translator, $locale);
    }

    /**
     * InvitationEmail constructor.
     *
     * @param UserInvitationInterface $invitation
     * @param string                  $acceptUrl
     * @param TranslatorInterface     $translator
     * @param string|null             $locale
     * @param Headers|null            $headers
     * @param AbstractPart|null       $body
     */
    public function __construct(UserInvitationInterface $invitation, string $acceptUrl, TranslatorInterface $translator, ?string $locale = null,  Headers $headers = null, AbstractPart $body = null)
    {
        parent::__construct($translator, $locale, $headers, $body);

        $this->setContextParam('invitation', $invitation);
        $this->setContextParam('acceptUrl', $acceptUrl);

        $this->setTranslationParams([
            '%name%' => $invitation->getName(),
            '%surname%' => $invitation->getSurname(),
            '%username%' => $invitation->getUsername(),
            '%email%' => $invitation->getEmail(),
            '%accept_url%' => $acceptUrl,
        ]);

        $this->htmlTemplate('@SfsUser/invite/invite.email.twig');

        $this->subject('invite.accept.email.subject', 'sfs_user');
    }
}