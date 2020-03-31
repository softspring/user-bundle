<?php

namespace Softspring\UserBundle\Form\Settings;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;

class PreferencesForm extends AbstractType implements PreferencesFormInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * RegisterForm constructor.
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'preferences.form.%name%.label',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $reflection = $this->userManager->getEntityClassReflection();

        if ($reflection->implementsInterface(NameSurnameInterface::class)) {
            $builder->add('name', Types\TextType::class);
            $builder->add('surname', Types\TextType::class);
        }
    }
}