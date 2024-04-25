<?php

namespace Softspring\UserBundle\Form\Admin;

use Softspring\AccountBundle\Model\MultiAccountedInterface;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDeleteForm extends AbstractType implements UserDeleteFormInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_users.delete.form.%name%.label',
            'validation_groups' => ['delete'],
        ]);

        //        $resolver->setDefined('user');
        //        $resolver->setAllowedTypes('user', [UserInterface::class]);
        //        $resolver->setRequired('user');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //        if ($options['user'] instanceof MultiAccountedInterface) {
        //            $accounts = $this->getDeletableAccounts($options['user']);
        //
        //            if (!empty($accounts)) {
        //                $builder->add('deleteOwnedAccounts', ChoiceType::class, [
        //                    'multiple' => true,
        //                    'expanded' => true,
        //                    'mapped' => false,
        //                    'choices' => $accounts,
        //                    'choice_label' => function (AccountInterface $account) {
        //                        return $account->getName();
        //                    },
        //                ]);
        //            }
        //        }
    }

    protected function getDeletableAccounts(MultiAccountedInterface $user): array
    {
        return [];

        //        $accountsForDeletion = [];
        //
        //        /** @var MultiAccountedInterface $user */
        //        foreach($user->getAccounts() as $account) {
        //            if ($account->getOwner() === $user) {
        //                $accountsForDeletion[] = $account;
        //            }
        //        }
        //
        //        return $accountsForDeletion;
    }
}
