<?php

namespace Softspring\UserBundle\Form;

use Softspring\User\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AcceptInvitationForm extends AbstractType implements AcceptInvitationFormInterface
{
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UserInterface::class,
            'csrf_token_id' => 'accept',
            'translation_domain' => 'sfs_user',
            'label_format' => 'invitation.accept.form.%name%.label',
            'validation_groups' => ['Accept', 'Default'],
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
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => [
                'translation_domain' => 'sfs_user',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'first_options' => array('label' => 'invitation.accept.form.password.label'),
            'second_options' => array('label' => 'invitation.accept.form.password_confirmation.label'),
            'invalid_message' => 'sfs_user.password.mismatch',
        ]);

        $builder->add('acceptConditions', CheckboxType::class,[
            'required' => true,
            'mapped' => false,
        ]);
    }
}
