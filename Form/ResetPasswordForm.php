<?php

namespace Softspring\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

class ResetPasswordForm extends AbstractType implements ResetPasswordFormInterface
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'sfs_user',
            'label_format' => 'reset_password.reset.form.%name%.label',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => [
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'first_options' => [
                'label' => 'reset_password.reset.form.newPassword.password.label',
            ],
            'second_options' => [
                'label' => 'reset_password.reset.form.newPassword.confirmation.label',
            ],
            'invalid_message' => 'reset_password.reset.form.newPassword.mismatch',
        ]);
    }
}