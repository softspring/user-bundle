<?php

namespace Softspring\UserBundle\Form\Admin;

use Jhg\DoctrinePagination\ORM\FilterRepositoryInterface;
use Softspring\CrudlBundle\Form\EntityListFilterForm;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserListFilterForm extends EntityListFilterForm implements UserListFilterFormInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * UserListFilterForm constructor.
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_users.list.filter_form.%name%.label',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $named = $this->userManager->getEntityClassReflection()->implementsInterface(NameSurnameInterface::class);
        $isFilterRepo = $this->userManager->getRepository() instanceof FilterRepositoryInterface;

        if ($named) {
            $builder->add('name', TextType::class, [
                'property_path' => $isFilterRepo ? '[name_like]' : '[name]',
            ]);
            $builder->add('surname', TextType::class, [
                'property_path' => $isFilterRepo ? '[surname_like]' : '[surname]',
            ]);
        }

        $builder->add('email', TextType::class, [
            'property_path' => $isFilterRepo ? '[email_like]' : '[email]',
        ]);

        $builder->add('search', SubmitType::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getRpp(Request $request): int
    {
        return 20;
    }
}
