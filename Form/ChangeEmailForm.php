<?php

namespace Softspring\UserBundle\Form;

use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

class ChangeEmailForm extends AbstractType implements ChangeEmailFormInterface
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'change_email.form.%name%.label',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('currentPassword', Types\PasswordType::class);
        $builder->add('email', Types\EmailType::class);
    }
}