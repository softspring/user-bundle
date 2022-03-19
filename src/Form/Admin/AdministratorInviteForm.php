<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\RolesAdminInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdministratorInviteForm extends AbstractType implements AdministratorInviteFormInterface
{
    protected UserInvitationManagerInterface $invitationManager;
    protected AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(UserInvitationManagerInterface $invitationManager, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->invitationManager = $invitationManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInvitationInterface::class,
            'csrf_token_id' => 'invitation',
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_administrators.invite.form.%name%.label',
            'validation_groups' => ['Default'],
        ]);
    }

    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $reflection = $this->invitationManager->getEntityClassReflection();

        if ($reflection->implementsInterface(NameSurnameInterface::class)) {
            $builder->add('name', TextType::class);
            $builder->add('surname', TextType::class);
        }

        $builder->add('username');
        $builder->add('email', EmailType::class);

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && $reflection->implementsInterface(RolesAdminInterface::class)) {
            $builder->add('superAdmin', CheckboxType::class, [
                'required' => false,
            ]);
        }
    }
}
