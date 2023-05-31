<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserIdentifierEmailInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserUpdateForm extends AbstractType implements UserUpdateFormInterface
{
    protected UserManagerInterface $userManager;
    protected ?array $locales;

    public function __construct(UserManagerInterface $userManager, array $locales = null)
    {
        $this->userManager = $userManager;
        $this->locales = $locales;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_users.update.form.%name%.label',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $reflection = $this->userManager->getEntityClassReflection();

        if ($reflection->implementsInterface(NameSurnameInterface::class)) {
            $builder->add('name', TextType::class);
            $builder->add('surname', TextType::class);
        }

        if ($reflection->implementsInterface(UserIdentifierUsernameInterface::class)) {
            $builder->add('username', TextType::class);
        }

        if ($reflection->implementsInterface(UserWithEmailInterface::class) || $reflection->implementsInterface(UserIdentifierEmailInterface::class)) {
            $builder->add('email', EmailType::class);
        }

        //        if ($reflection->implementsInterface(UserHasLocalePreferenceInterface::class)) {
        //            $builder->add('locale', LocaleType::class, [
        //                'choice_loader' => null,
        //                'choices' => $this->locales,
        //            ]);
        //        }
    }
}
