<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\Account\Model\AccountInterface;
use Softspring\Account\Model\MultiAccountedInterface;
use Softspring\User\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDeleteForm extends AbstractType implements UserDeleteFormInterface
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_users.delete.form.%name%.label',
        ]);

        $resolver->setDefined('user');
        $resolver->setAllowedTypes('user', [UserInterface::class]);
        $resolver->setRequired('user');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['user'] instanceof MultiAccountedInterface) {
            $accounts = $this->getDeletableAccounts($options['user']);

            if (!empty($accounts)) {
                $builder->add('deleteOwnedAccounts', ChoiceType::class, [
                    'multiple' => true,
                    'expanded' => true,
                    'mapped' => false,
                    'choices' => $accounts,
                    'choice_label' => function (AccountInterface $account) {
                        return $account->getName();
                    },
                ]);
            }
        }
    }

    protected function getDeletableAccounts(MultiAccountedInterface $user): array
    {
        $accountsForDeletion = [];

        /** @var MultiAccountedInterface $user */
        foreach($user->getAccounts() as $account) {
            if ($account->getOwner() === $user) {
                $accountsForDeletion[] = $account;
            }
        }

        return $accountsForDeletion;
    }
}