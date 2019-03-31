<?php

namespace Softspring\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginForm extends AbstractType implements LoginFormInterface
{
    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * LoginController constructor.
     *
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'sfs_user',
            'label_format' => 'login.form.%name%.label',
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
            'csrf_token_manager' => $this->csrfTokenManager,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_username', TextType::class);
        $builder->add('_password', PasswordType::class);
    }
}