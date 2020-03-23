<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitationCreateForm extends AbstractType implements InvitationCreateFormInterface
{
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UserInvitationInterface::class,
            'csrf_token_id' => 'invitation',
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_invitations.form.%name%.label',
            'validation_groups' => ['Default'],
        ));
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('surname');
        $builder->add('username');
        $builder->add('email', EmailType::class);
        $builder->add('admin', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('superAdmin', CheckboxType::class, [
            'required' => false,
        ]);
    }
}
