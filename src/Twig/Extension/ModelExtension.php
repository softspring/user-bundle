<?php

namespace Softspring\UserBundle\Twig\Extension;

use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModelExtension extends AbstractExtension
{
    /**
     * @var UserManagerInterface|null
     */
    protected $userManager;

    /**
     * @var UserAccessManagerInterface|null
     */
    protected $accessManager;

    /**
     * ModelExtension constructor.
     *
     * @param UserManagerInterface|null       $userManager
     * @param UserAccessManagerInterface|null $accessManager
     */
    public function __construct(?UserManagerInterface $userManager, ?UserAccessManagerInterface $accessManager)
    {
        $this->userManager = $userManager;
        $this->accessManager = $accessManager;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('sfs_user_is', [$this, 'userCheckInterface']),
            new TwigFunction('sfs_user_access_is', [$this, 'userAccessCheckInterface']),
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