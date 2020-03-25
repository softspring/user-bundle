<?php

namespace Softspring\UserBundle\Mime\Example\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordEmailForm extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);
        $builder->add('surname', TextType::class);
        $builder->add('username', TextType::class);
        $builder->add('email', TextType::class);
    }
}