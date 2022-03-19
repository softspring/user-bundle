<?php

namespace Softspring\UserBundle\Form\Admin;

use Jhg\DoctrinePagination\ORM\FilterRepositoryInterface;
use Softspring\CrudlBundle\Form\EntityListFilterForm;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserListFilterForm extends EntityListFilterForm implements UserListFilterFormInterface
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
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $isFilterRepo = $this->userManager->getRepository() instanceof FilterRepositoryInterface;
        $hasFields = false;

        if ($this->userManager->getEntityClassReflection()->implementsInterface(NameSurnameInterface::class)) {
            $builder->add('name', TextType::class, [
                'property_path' => $isFilterRepo ? '[name_like]' : '[name]',
            ]);
            $builder->add('surname', TextType::class, [
                'property_path' => $isFilterRepo ? '[surname_like]' : '[surname]',
            ]);
            $hasFields = true;
        }

        if ($this->userManager->getEntityClassReflection()->implementsInterface(UserWithEmailInterface::class)) {
            $builder->add('email', TextType::class, [
                'property_path' => $isFilterRepo ? '[email_like]' : '[email]',
            ]);
            $hasFields = true;
        }

        if ($hasFields) {
            $builder->add('search', SubmitType::class);
        }
    }

    public static function orderValidFields(): array
    {
        return ['name', 'surname', 'email', 'lastLogin'];
    }

    public static function orderDefaultField(): string
    {
        return 'surname';
    }

    public function getRpp(Request $request): int
    {
        return 10;
    }
}
