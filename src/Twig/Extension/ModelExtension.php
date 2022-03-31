<?php

namespace Softspring\UserBundle\Twig\Extension;

use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModelExtension extends AbstractExtension
{
    protected ?UserManagerInterface $userManager;

    protected ?UserAccessManagerInterface $accessManager;

    protected ?UserInvitationManagerInterface $invitationManager;

    public function __construct(?UserManagerInterface $userManager, ?UserAccessManagerInterface $accessManager, ?UserInvitationManagerInterface $invitationManager)
    {
        $this->userManager = $userManager;
        $this->accessManager = $accessManager;
        $this->invitationManager = $invitationManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sfs_user_is', [$this, 'userCheckInterface']),
            new TwigFunction('sfs_user_access_is', [$this, 'userAccessCheckInterface']),
            new TwigFunction('sfs_invitation_is', [$this, 'userInvitationInterface']),
        ];
    }

    public function userCheckInterface(string $interface): bool
    {
        if (!$this->userManager instanceof UserManagerInterface) {
            return false;
        }

        return $this->checkImplements($this->userManager->getEntityClassReflection(), $interface);
    }

    public function userAccessCheckInterface(string $interface): bool
    {
        if (!$this->accessManager instanceof UserAccessManagerInterface) {
            return false;
        }

        return $this->checkImplements($this->accessManager->getEntityClassReflection(), $interface);
    }

    public function userInvitationInterface(string $interface): bool
    {
        if (!$this->invitationManager instanceof UserInvitationManagerInterface) {
            return false;
        }

        return $this->checkImplements($this->invitationManager->getEntityClassReflection(), $interface);
    }

    protected function checkImplements(\ReflectionClass $reflectionClass, string $interface): bool
    {
        $interface = ucfirst($interface);

        $options = [
            "Softspring\\UserBundle\\Model\\{$interface}Interface", // if string is "NameSurname" (partial model name)
            "Softspring\\UserBundle\\Model\\User{$interface}Interface",
            "Softspring\\UserBundle\\Model\\UserWith{$interface}Interface",
            "Softspring\\UserBundle\\Model\\UserAccess{$interface}Interface",
            "Softspring\\UserBundle\\Model\\{$interface}", // if string is "NameSurnameInterface" (model name)
            $interface, // if string is "Softspring\\UserBundle\\Model\\NameSurnameInterface" (fully qualified name)
        ];

        foreach ($options as $option) {
            if (interface_exists($option) && $reflectionClass->implementsInterface($option)) {
                return true;
            }
        }

        return false;
    }
}
