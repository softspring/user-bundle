<?php

namespace Softspring\UserBundle\Form;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserIdentifierEmailInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserPasswordInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterForm extends AbstractType implements RegisterFormInterface
{
    protected UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'register.form.%name%.label',
            'validation_groups' => ['Register', 'Default'],
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $reflection = $this->userManager->getEntityClassReflection();

        if ($reflection->implementsInterface(NameSurnameInterface::class)) {
            $builder->add('name', Types\TextType::class, [
                'attr' => [
                    'autocomplete' => 'given-name',
                ],
            ]);
            $builder->add('surname', Types\TextType::class, [
                'attr' => [
                    'autocomplete' => 'family-name',
                ],
            ]);
        }

        if ($reflection->implementsInterface(UserIdentifierUsernameInterface::class)) {
            $builder->add('username', Types\TextType::class, [
                'attr' => [
                    'autocomplete' => 'username',
                ],
            ]);
        }

        if ($reflection->implementsInterface(UserWithEmailInterface::class) || $reflection->implementsInterface(UserIdentifierEmailInterface::class)) {
            $builder->add('email', Types\EmailType::class, [
                'attr' => [
                    'autocomplete' => 'email',
                ],
            ]);
        }

        if ($reflection->implementsInterface(UserPasswordInterface::class)) {
            $builder->add('plainPassword', Types\PasswordType::class, [
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ]);
        }

        $builder->add('acceptConditions', Types\CheckboxType::class, [
            'required' => true,
            'mapped' => false,
        ]);
    }
}
