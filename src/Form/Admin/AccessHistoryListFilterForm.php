<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\Component\DoctrinePaginator\Form\PaginatorForm;
use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Model\UserAccessInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccessHistoryListFilterForm extends PaginatorForm implements AccessHistoryListFilterFormInterface
{
    protected UserAccessManagerInterface $accessManager;

    public function __construct(UserAccessManagerInterface $accessManager)
    {
        parent::__construct($accessManager->getEntityManager());
        $this->accessManager = $accessManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'class' => UserAccessInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_access_history.list.filter_form.%name%.label',
            'rpp_valid_values' => [50],
            'rpp_default_value' => 50,
            'order_valid_fields' => ['loginAt'],
            'order_default_value' => 'loginAt',
            'order_direction_default_value' => 'desc',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('search', SubmitType::class);
    }
}
