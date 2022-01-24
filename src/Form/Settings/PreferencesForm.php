<?php

namespace Softspring\UserBundle\Form\Settings;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserHasLocalePreferenceInterface;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreferencesForm extends AbstractType implements PreferencesFormInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var array
     */
    protected $locales;

    /**
     * PreferencesForm constructor.
     */
    public function __construct(UserManagerInterface $userManager, array $locales)
    {
        $this->userManager = $userManager;
        $this->locales = $locales;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'sfs_user',
            'label_format' => 'preferences.form.%name%.label',
            'locales' => null,
        ]);

        $resolver->setAllowedTypes('locales', ['null', 'array']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $reflection = $this->userManager->getEntityClassReflection();

        if ($reflection->implementsInterface(NameSurnameInterface::class)) {
            $builder->add('name', Types\TextType::class);
            $builder->add('surname', Types\TextType::class);
        }

        if ($reflection->implementsInterface(UserHasLocalePreferenceInterface::class)) {
            $locales = !empty($options['locales']) ? $options['locales'] : $this->locales;
            $builder->add('locale', Types\ChoiceType::class, [
                'choices' => array_combine($locales, $locales),
            ]);
        }
    }
}
