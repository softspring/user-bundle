<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdministratorUpdateForm extends AbstractType implements AdministratorUpdateFormInterface
{
    protected UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_administrators.update.form.%name%.label',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $named = $this->userManager->getEntityClassReflection()->implementsInterface(NameSurnameInterface::class);

        if ($named) {
            $builder->add('name');
            $builder->add('surname');
        }

        if ($this->userManager->getEntityClassReflection()->implementsInterface(UserIdentifierUsernameInterface::class)) {
            $builder->add('username');
        }

        if ($this->userManager->getEntityClassReflection()->implementsInterface(UserWithEmailInterface::class)) {
            $builder->add('email', EmailType::class);
        }
    }
}
