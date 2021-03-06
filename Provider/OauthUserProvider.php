<?php

namespace Softspring\UserBundle\Provider;

use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\Oauth\FacebookOauthInterface;
use Softspring\UserBundle\Model\UserInterface as SoftspringUserInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class OauthUserProvider
 *
 * @see HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider
 */
class OauthUserProvider implements UserProviderInterface, AccountConnectorInterface, OAuthAwareUserProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var array
     */
    protected $properties = array(
        'identifier' => 'id',
    );

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @var array
     */
    protected $oauthServices;

    /**
     * OauthUserProvider constructor.
     *
     * @param UserManagerInterface $userManager
     * @param array                $properties
     * @param array                $oauthServices
     */
    public function __construct(UserManagerInterface $userManager, array $properties, array $oauthServices)
    {
        $this->userManager = $userManager;
        $this->properties = array_merge($this->properties, $properties);
        $this->oauthServices = $oauthServices;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        return $this->userManager->findUserByUsername($username);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername(); // provides id (identifier)

        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        if (null === $user || null === $username) {
            $user = $this->userManager->findUserByEmail($response->getEmail());
            if ($user) {
                $this->linkUser($user, $response);
                $this->userManager->saveEntity($user);
                return $user;
            }

            if ($this->oauthServices[$response->getResourceOwner()->getName()]['login_create']) {
                $user = $this->createUser($response);
                $this->userManager->saveEntity($user);
                return $user;
            }

            throw new AccountNotLinkedException(sprintf("User '%s' not found.", $response->getUsername()));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        if (!$user instanceof SoftspringUserInterface) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', SoftspringUserInterface::class, get_class($user)));
        }

        $property = $this->getProperty($response);
        $username = $response->getUsername();

        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $this->disconnect($previousUser, $response);
        }

        if ($this->accessor->isWritable($user, $property)) {
            $this->accessor->setValue($user, $property, $username);
        } else {
            throw new \RuntimeException(sprintf('Could not determine access type for property "%s".', $property));
        }

        $this->userManager->saveEntity($user);
    }

    /**
     * Disconnects a user.
     *
     * @param UserInterface|SoftspringUserInterface $user
     * @param UserResponseInterface                 $response
     */
    public function disconnect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);

        $this->accessor->setValue($user, $property, null);
        $this->userManager->saveEntity($user);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $identifier = $this->properties['identifier'];
        if (!$user instanceof SoftspringUserInterface || !$this->accessor->isReadable($user, $identifier)) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', SoftspringUserInterface::class, get_class($user)));
        }

        $userId = $this->accessor->getValue($user, $identifier);
        if (null === $user = $this->userManager->findUserBy(array($identifier => $userId))) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $userId));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        $userClass = $this->userManager->getEntityClass();

        return $userClass === $class || is_subclass_of($class, $userClass);
    }

    /**
     * Gets the property for the response.
     *
     * @param UserResponseInterface $response
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getProperty(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        return $this->properties[$resourceOwnerName];
    }


    /**
     * @param UserResponseInterface $response
     *
     * @return SoftspringUserInterface
     * @throws \Exception
     */
    protected function createUser(UserResponseInterface $response): SoftspringUserInterface
    {
        /** @var SoftspringUserInterface $user */
        $user = $this->userManager->createEntity();
//        $user->setLastLogin(new \DateTime());

        $user->setEnabled(true);
        $user->setUsername($response->getUsername());
        $user->setPassword('invalid-oauth');
        $user->setRoles(['ROLE_USER']);
        $user->setEmail($response->getEmail());

        if ($user instanceof NameSurnameInterface) {
            $user->setName($response->getFirstName());
            $user->setSurname($response->getLastName());
        }

        $this->linkUser($user, $response);

        return $user;
    }

    protected function linkUser(SoftspringUserInterface $user, UserResponseInterface $response)
    {
        switch ($response->getResourceOwner()->getName()) {
            case 'facebook':
                if (!$user instanceof FacebookOauthInterface) {
                    throw new \Exception(sprintf('Your entity does not extend %s interface. Check documentation.', FacebookOauthInterface::class));
                }
                $user->setFacebookUserId($response->getUsername());
                break;

            default:
                throw new \Exception('Integration not yet implemented');
        }
    }
}