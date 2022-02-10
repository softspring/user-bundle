<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\CrudlBundle\Form\EntityListFilterForm;
use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccessHistoryListFilterForm extends EntityListFilterForm implements AccessHistoryListFilterFormInterface
{
    protected UserAccessManagerInterface $accessManager;

    /**
     * AccessHistoryListFilterForm constructor.
     */
    public function __construct(UserAccessManagerInterface $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_access_history.list.filter_form.%name%.label',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('search', SubmitType::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getRpp(Request $request): int
    {
        return 50;
    }

    public function getOrder(Request $request): array
    {
        return ['loginAt' => 'DESC'];
    }
}
