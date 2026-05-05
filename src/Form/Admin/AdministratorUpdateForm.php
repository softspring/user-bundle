<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Form\Admin;

use Softspring\MediaBundle\Form\MediaTypeUploadType;
use Softspring\UserBundle\Manager\AdminUserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserAvatarInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserMediaAvatarInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdministratorUpdateForm extends AbstractType implements AdministratorUpdateFormInterface
{
    protected AdminUserManagerInterface $userManager;

    public function __construct(AdminUserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'admin_administrators.update.form.%name%.label',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $reflection = $this->userManager->getEntityClassReflection();

        $named = $reflection->implementsInterface(NameSurnameInterface::class);
        if ($named) {
            $builder->add('name');
            $builder->add('surname');
        }

        if ($reflection->implementsInterface(UserIdentifierUsernameInterface::class)) {
            $builder->add('username');
        }

        if ($reflection->implementsInterface(UserWithEmailInterface::class)) {
            $builder->add('email', EmailType::class);
        }

        if ($reflection->implementsInterface(UserMediaAvatarInterface::class)) {
            $builder->add('avatarMedia', MediaTypeUploadType::class, [
                'media_type' => 'user_avatar',
                'allow_name_field' => false,
                'allow_description_field' => false,
                'required' => false,
            ]);
        } elseif ($reflection->implementsInterface(UserAvatarInterface::class)) {
            $builder->add('avatarUrl', UrlType::class);
        }
    }
}
