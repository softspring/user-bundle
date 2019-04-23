<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\User\Model\UserInvitationInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitationForm extends AbstractType implements InvitationFormInterface
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
        $builder->add('email', EmailType::class);
        $builder->add('username');
        $builder->add('name');
        $builder->add('surname');
    }
}
