<?php

namespace Softspring\UserBundle\Form\Settings;

use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeUsernameForm extends AbstractType implements ChangeUsernameFormInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'change_username.form.%name%.label',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('currentPassword', Types\PasswordType::class);
        $builder->add('username', Types\TextType::class);
    }
}
