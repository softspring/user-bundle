<?php

namespace Softspring\UserBundle\Twig\Extension;

use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\UserAccessLatLongInterface;
use Softspring\UserBundle\Model\UserAccessLocationInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
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
            new TwigFunction('sfs_user_is_name_surname', [$this, 'userIsNameSurnameInterface']),
            new TwigFunction('sfs_user_is_confirmable', [$this, 'userIsConfirmableInterface']),
            new TwigFunction('sfs_user_is_emailed', [$this, 'userWithEmailInterface']),
            new TwigFunction('sfs_user_access_is_location', [$this, 'userAccessLocationInterface']),
            new TwigFunction('sfs_user_access_is_latlong', [$this, 'userAccessLatLongInterface']),
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

    /**
     * @return bool
     */
    public function userIsConfirmableInterface(): bool
    {
        if (!$this->userManager instanceof UserManagerInterface) {
            return false;
        }

        return $this->userManager->getEntityClassReflection()->implementsInterface(ConfirmableInterface::class);
    }

    /**
     * @return bool
     */
    public function userWithEmailInterface(): bool
    {
        if (!$this->userManager instanceof UserManagerInterface) {
            return false;
        }

        return $this->userManager->getEntityClassReflection()->implementsInterface(UserWithEmailInterface::class);
    }

    /**
     * @return bool
     */
    public function userAccessLocationInterface(): bool
    {
        if (!$this->accessManager instanceof UserAccessManagerInterface) {
            return false;
        }

        return $this->accessManager->getEntityClassReflection()->implementsInterface(UserAccessLocationInterface::class);
    }

    /**
     * @return bool
     */
    public function userAccessLatLongInterface(): bool
    {
        if (!$this->accessManager instanceof UserAccessManagerInterface) {
            return false;
        }

        return $this->accessManager->getEntityClassReflection()->implementsInterface(UserAccessLatLongInterface::class);
    }
}