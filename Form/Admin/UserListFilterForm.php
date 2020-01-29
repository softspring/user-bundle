<?php

namespace Softspring\UserBundle\Form\Admin;

use Jhg\DoctrinePagination\ORM\FilterRepositoryInterface;
use Softspring\CrudlBundle\Form\EntityListFilterForm;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $named = $this->userManager->getEntityClassReflection()->implementsInterface(NameSurnameInterface::class);

        if ($this->userManager->getRepository() instanceof FilterRepositoryInterface) {
            if ($named) {
                $builder->add('name_like');
                $builder->add('surname_like');
            }

            $builder->add('email_like');
        } else {
            if ($named) {
                $builder->add('name');
                $builder->add('surname');
            }

            $builder->add('email');
        }

        $builder->add('search', SubmitType::class);
    }

    /**
     * @inheritDoc
     */
    public function getRpp(Request $request): int
    {
        return 20;
    }
}