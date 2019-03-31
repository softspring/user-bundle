<?php

namespace Softspring\UserBundle\Form;

use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

class RegisterForm extends AbstractType implements RegisterFormInterface
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'register.form.%name%.label',
            'validation_groups'=> ['Register', 'Default'],
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Types\TextType::class);
        $builder->add('surname', Types\TextType::class);
        $builder->add('username', Types\TextType::class);
        $builder->add('email', Types\EmailType::class);
        $builder->add('plainPassword', Types\PasswordType::class);
        $builder->add('acceptConditions', Types\CheckboxType::class,[
            'required' => true,
            'mapped' => false,
        ]);
    }
}