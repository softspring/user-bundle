<?php

namespace Softspring\UserBundle\Form;

use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginForm extends AbstractType implements LoginFormInterface
{
    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * @var FirewallMap
     */
    protected $firewallMap;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * LoginForm constructor.
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, FirewallMap $firewallMap, RequestStack $requestStack)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->firewallMap = $firewallMap;
        $this->requestStack = $requestStack;
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

        if ($this->isRememberMeEnabled()) {
            // TODO obtain parameter_name from firewall configuration
            $builder->add('_remember_me', CheckboxType::class, [
                'required' => false,
                'data' => true,
            ]);
        }
    }

    protected function isRememberMeEnabled(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $firewallConfig = $this->firewallMap->getFirewallConfig($request);

        return in_array('remember_me', $firewallConfig->getListeners());
    }
}
