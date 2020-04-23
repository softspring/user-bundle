<?php

namespace Softspring\UserBundle\Mime;

use Softspring\MailerBundle\Mime\TranslatableEmail;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Contracts\Translation\TranslatorInterface;

class InvitationEmail extends TranslatableEmail
{
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

        $this->context['invitation'] = $invitation;
        $this->context['acceptUrl'] = $acceptUrl;

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