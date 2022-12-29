<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\Component\DoctrinePaginator\Form\PaginatorFiltersForm;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserListFilterForm extends PaginatorFiltersForm implements UserListFilterFormInterface
{
    protected UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_users.list.filter_form.%name%.label',
            'rpp_valid_values' => [20],
            'rpp_default_value' => 20,
            'order_valid_fields' => ['name', 'surname', 'email', 'lastLogin'],
            'order_default_value' => 'surname',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $hasFields = false;

        if ($this->userManager->getEntityClassReflection()->implementsInterface(NameSurnameInterface::class)) {
            $builder->add('name', TextType::class, [
                'property_path' => '[name__like]',
            ]);
            $builder->add('surname', TextType::class, [
                'property_path' => '[surname__like]',
            ]);
            $hasFields = true;
        }

        if ($this->userManager->getEntityClassReflection()->implementsInterface(UserWithEmailInterface::class)) {
            $builder->add('email', TextType::class, [
                'property_path' => '[email__like]',
            ]);
            $hasFields = true;
        }

        if ($hasFields) {
            $builder->add('search', SubmitType::class);
        }
    }
}
