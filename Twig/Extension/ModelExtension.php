<?php

namespace Softspring\UserBundle\Twig\Extension;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModelExtension extends AbstractExtension
{
    /**
     * @var UserManagerInterface|null
     */
    protected $userManager;

    /**
     * ModelExtension constructor.
     *
     * @param UserManagerInterface|null $userManager
     */
    public function __construct(?UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('sfs_user_is_name_surname', [$this, 'userIsNameSurnameInterface']),
        ];
    }

    /**
     * @return bool
     */
    public function userIsNameSurnameInterface(): bool
    {
        if (!$this->userManager instanceof UserManagerInterface) {
            return false;
        }

        return $this->userManager->getEntityClassReflection()->implementsInterface(NameSurnameInterface::class);
    }
}