<?php

namespace Softspring\UserBundle\Mailer\Loader;

use Softspring\MailerBundle\Loader\TemplateLoaderInterface;
use Softspring\MailerBundle\Model\Template;
use Softspring\MailerBundle\Model\TemplateCollection;

class MailTemplateLoader implements TemplateLoaderInterface
{
    /**
     * @var string|null
     */
    protected $inviteClass;

    /**
     * MailTemplateLoader constructor.
     * @param string|null $inviteClass
     */
    public function __construct(?string $inviteClass)
    {
        $this->inviteClass = $inviteClass;
    }

    /**
     * @inheritdoc
     */
    public function load(): TemplateCollection
    {
        $collection = new TemplateCollection();

        $template = new Template();
        $template->setId('sfs_user.reset_password');
        $template->setName('User password resetting request');
        $template->setTwigTemplate('@SfsUser/reset_password/resetting.email.twig');
        $template->setExampleContext([
            'user_name' => 'Mathew',
            'user_surname' => 'Smith',
            'user_username' => 'mathewsmith',
            'user_email' => 'mathewsmith@example.local',
            'resetUrl' => '#',
        ]);
        // TODO SET FROM EMAIL AND NAME (WITH DEFAULT FALLBACK)
        $collection->addTemplate($template);

        $template = new Template();
        $template->setId('sfs_user.register_confirm');
        $template->setName('User register confirmation');
        $template->setTwigTemplate('@SfsUser/register/confirm.email.twig');
        $template->setExampleContext([
            'user_name' => 'Mathew',
            'user_surname' => 'Smith',
            'user_username' => 'mathewsmith',
            'user_email' => 'mathewsmith@example.local',
            'confirmUrl' => '#',
        ]);
        // TODO SET FROM EMAIL AND NAME (WITH DEFAULT FALLBACK)
        $collection->addTemplate($template);

        if (!empty($this->inviteClass)) {
            $template = new Template();
            $template->setId('sfs_user.invite');
            $template->setName('Invitation for a user');
            $template->setTwigTemplate('@SfsUser/invite/invite.email.twig');
            $template->setExampleContext([
                'user_name' => 'Mathew',
                'user_surname' => 'Smith',
                'user_username' => 'mathewsmith',
                'user_email' => 'mathewsmith@example.local',
                'acceptUrl' => '#',
            ]);
            // TODO SET FROM EMAIL AND NAME (WITH DEFAULT FALLBACK)
            $collection->addTemplate($template);
        }

        return $collection;
    }
}