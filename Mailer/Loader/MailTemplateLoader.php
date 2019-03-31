<?php

namespace Softspring\UserBundle\Mailer\Loader;

use Softspring\MailerBundle\Loader\TemplateLoaderInterface;
use Softspring\MailerBundle\Model\Template;
use Softspring\MailerBundle\Model\TemplateCollection;

class MailTemplateLoader implements TemplateLoaderInterface
{
    /**
     * @inheritdoc
     */
    public function load(): TemplateCollection
    {
        $collection = new TemplateCollection();

        $template = new Template();
        $template->setId('sfs_user.reset_password');
        $template->setTwigTemplate('@SfsUser/resetting/resetting.email.twig');
        // TODO SET FROM EMAIL AND NAME (WITH DEFAULT FALLBACK)
        $collection->addTemplate($template);

        $template = new Template();
        $template->setId('sfs_user.register_confirm');
        $template->setTwigTemplate('@SfsUser/register/confirm.email.twig');
        // TODO SET FROM EMAIL AND NAME (WITH DEFAULT FALLBACK)
        $collection->addTemplate($template);

        $template = new Template();
        $template->setId('sfs_user.invite');
        $template->setTwigTemplate('@SfsUser/invite/invite.email.twig');
        // TODO SET FROM EMAIL AND NAME (WITH DEFAULT FALLBACK)
        $collection->addTemplate($template);

//        $template->setExampleContext([
//            'user' => [
//                'username' => 'myusername',
//            ],
//            'confirmationUrl' => 'https://myapplication.com/resetting?code=123456879',
//        ]);

        return $collection;
    }
}