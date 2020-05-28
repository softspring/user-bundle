<?php

namespace Softspring\UserBundle\Mailer\Loader;

use Softspring\MailerBundle\Template\Template;
use Softspring\MailerBundle\Template\Loader\TemplateLoaderInterface;
use Softspring\MailerBundle\Template\TemplateCollection;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Mime\ConfirmationEmail;
use Softspring\UserBundle\Mime\Example\ConfirmationEmailExample;
use Softspring\UserBundle\Mime\Example\InvitationEmailExample;
use Softspring\UserBundle\Mime\Example\ResetPasswordEmailExample;
use Softspring\UserBundle\Mime\InvitationEmail;
use Softspring\UserBundle\Mime\ResetPasswordEmail;
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
        $template->setClass(ResetPasswordEmail::class);
        $template->setPreview(true);
        $collection->addTemplate($template);

        if ($this->userManager->getEntityClassReflection()->implementsInterface(ConfirmableInterface::class)) {
            $template = new Template();
            $template->setId('sfs_user.register_confirm');
            $template->setClass(ConfirmationEmail::class);
            $template->setPreview(true);
            $collection->addTemplate($template);
        }

        if (!empty($this->inviteClass)) {
            $template = new Template();
            $template->setId('sfs_user.invite');
            $template->setClass(InvitationEmail::class);
            $template->setPreview(true);
            $collection->addTemplate($template);
        }

        return $collection;
    }
}