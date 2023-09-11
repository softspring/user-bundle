<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\Component\DoctrinePaginator\Form\PaginatorForm;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitationListFilterForm extends PaginatorForm implements InvitationListFilterFormInterface
{
    protected UserInvitationManagerInterface $invitationManager;

    public function __construct(UserInvitationManagerInterface $invitationManager)
    {
        parent::__construct($invitationManager->getEntityManager());
        $this->invitationManager = $invitationManager;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_invitations.list.filter_form.%name%.label',
            'class' => UserInvitationInterface::class,
            'rpp_valid_values' => [20],
            'rpp_default_value' => 20,
            'order_valid_fields' => ['email', 'name', 'surname', 'username', 'acceptedAt'],
            'order_default_value' => 'email',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        if ($this->invitationManager->getEntityClassReflection()->implementsInterface(NameSurnameInterface::class)) {
            $builder->add('name', TextType::class, [
                'property_path' => '[name__like]',
            ]);
            $builder->add('surname', TextType::class, [
                'property_path' => '[surname__like]',
            ]);
        }

        if ($this->invitationManager->getEntityClassReflection()->implementsInterface(UserWithEmailInterface::class)) {
            $builder->add('email', TextType::class, [
                'property_path' => '[email__like]',
            ]);
        }

        $builder->add('status', ChoiceType::class, [
            'choices' => [
                'admin_invitations.list.filter_form.status.any' => '',
                'admin_invitations.list.filter_form.status.accepted' => 'not_null',
                'admin_invitations.list.filter_form.status.pending' => 'null',
            ],
            'property_path' => '[acceptedAt_is]',
        ]);

        $builder->add('search', SubmitType::class);
    }
}
