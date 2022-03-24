<?php

namespace Softspring\UserBundle\Form\Admin;

use Jhg\DoctrinePagination\ORM\FilterRepositoryInterface;
use Softspring\CrudlBundle\Form\EntityListFilterForm;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitationListFilterForm extends EntityListFilterForm implements InvitationListFilterFormInterface
{
    protected UserInvitationManagerInterface $invitationManager;

    public function __construct(UserInvitationManagerInterface $invitationManager)
    {
        $this->invitationManager = $invitationManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_invitations.list.filter_form.%name%.label',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $isFilterRepo = $this->invitationManager->getRepository() instanceof FilterRepositoryInterface;

        if ($this->invitationManager->getEntityClassReflection()->implementsInterface(NameSurnameInterface::class)) {
            $builder->add('name', TextType::class, [
                'property_path' => $isFilterRepo ? '[name_like]' : '[name]',
            ]);
            $builder->add('surname', TextType::class, [
                'property_path' => $isFilterRepo ? '[surname_like]' : '[surname]',
            ]);
        }

        if ($this->invitationManager->getEntityClassReflection()->implementsInterface(UserWithEmailInterface::class)) {
            $builder->add('email', TextType::class, [
                'property_path' => $isFilterRepo ? '[email_like]' : '[email]',
            ]);
        }

        $builder->add('status', ChoiceType::class, [
            'choices' => [
                'admin_invitations.list.filter_form.status.any' => '',
                'admin_invitations.list.filter_form.status.accepted' => 'not_null',
                'admin_invitations.list.filter_form.status.pending' => 'null',
            ],
            'property_path' => $isFilterRepo ? '[acceptedAt_is]' : '[acceptedAt]',
        ]);

        $builder->add('search', SubmitType::class);
    }

    public static function orderValidFields(): array
    {
        return ['email', 'name', 'surname', 'username', 'acceptedAt'];
    }

    public static function orderDefaultField(): string
    {
        return 'email';
    }

    public function getRpp(Request $request): int
    {
        return 10;
    }
}
