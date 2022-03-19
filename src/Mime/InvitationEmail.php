<?php

namespace Softspring\UserBundle\Mime;

use Softspring\Component\MimeTranslatable\ExampleEmailInterface;
use Softspring\Component\MimeTranslatable\TranslatableEmail;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Contracts\Translation\TranslatorInterface;

class InvitationEmail extends TranslatableEmail implements ExampleEmailInterface
{
    public static function generateExample(TranslatorInterface $translator, ?string $locale = null): TranslatableEmail
    {
        $user = new ExampleInvitation();
        $user->setName('Mery');
        $user->setSurname('McCarty');
        $user->setEmail('mery_mccarty@example.com');
        $acceptUrl = '#accept-url';

        return new self($user, $acceptUrl, $translator, $locale);
    }

    /**
     * InvitationEmail constructor.
     */
    public function __construct(UserInvitationInterface $invitation, string $acceptUrl, TranslatorInterface $translator, ?string $locale = null, Headers $headers = null, AbstractPart $body = null)
    {
        parent::__construct($translator, $locale, $headers, $body);

        $this->setContextParam('invitation', $invitation);
        $this->setContextParam('acceptUrl', $acceptUrl);

        $this->setTranslationParams([
            '%name%' => $invitation instanceof NameSurnameInterface ? $invitation->getName() : $invitation->getUsername(),
            '%surname%' => $invitation instanceof NameSurnameInterface ? $invitation->getSurname() : '',
            '%username%' => $invitation->getUsername(),
            '%email%' => $invitation->getEmail(),
            '%accept_url%' => $acceptUrl,
        ]);

        $this->htmlTemplate('@SfsUser/invite/invite.email.twig');

        $this->subject('invite.accept.email.subject', 'sfs_user');
    }
}
