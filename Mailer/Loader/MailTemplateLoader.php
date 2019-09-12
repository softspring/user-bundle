<?php

namespace Softspring\UserBundle\Mailer\Loader;

use Softspring\MailerBundle\Loader\TemplateLoaderInterface;
use Softspring\MailerBundle\Model\Template;
use Softspring\MailerBundle\Model\TemplateCollection;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;

class MailTemplateLoader implements TemplateLoaderInterface
{
    /**
     * @var string|null
     */
    protected $inviteClass;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * MailTemplateLoader constructor.
     *
     * @param string|null          $inviteClass
     * @param UserManagerInterface $userManager
     */
    public function __construct(?string $inviteClass, UserManagerInterface $userManager)
    {
        $this->inviteClass = $inviteClass;
        $this->userManager = $userManager;
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

        $userReflection = new \ReflectionClass($this->userManager->getClass());
        if ($userReflection->implementsInterface(ConfirmableInterface::class)) {
            $template = new Template();
            $template->setId('sfs_user.register_confirm');
            $template->setName('User register confirmation');
            $template->setTwigTemplate('@SfsUser/register/confirm.email.twig');
            $template->setExampleContext([
                'user_name' => 'Mathew',
                'user_surname' => 'Smith',
                'user_username' => 'mathewsmith',
                'user_email' => 'mathewsmith@example.local',
                'confirmationUrl' => '#',
            ]);
            // TODO SET FROM EMAIL AND NAME (WITH DEFAULT FALLBACK)
            $collection->addTemplate($template);
        }

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