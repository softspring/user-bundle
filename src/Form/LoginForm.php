<?php

namespace Softspring\UserBundle\Form;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserIdentifierEmailInterface;
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
    protected CsrfTokenManagerInterface $csrfTokenManager;

    protected FirewallMap $firewallMap;

    protected RequestStack $requestStack;

    protected UserManagerInterface $userManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, FirewallMap $firewallMap, RequestStack $requestStack, UserManagerInterface $userManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->firewallMap = $firewallMap;
        $this->requestStack = $requestStack;
        $this->userManager = $userManager;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
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

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('_username', TextType::class, [
            'label' => $this->userManager->getEntityClassReflection()->implementsInterface(UserIdentifierEmailInterface::class) ? 'login.form.email.label' : 'login.form.username.label',
        ]);
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

        return in_array('remember_me', $firewallConfig->getAuthenticators());
    }
}
