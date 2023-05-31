<?php

namespace Softspring\UserBundle\Provider;

use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\EnablableInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\Oauth\FacebookOauthInterface;
use Softspring\UserBundle\Model\RolesInterface;
use Softspring\UserBundle\Model\UserIdentifierUsernameInterface;
use Softspring\UserBundle\Model\UserInterface as SoftspringUserInterface;
use Softspring\UserBundle\Model\UserPasswordInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class OauthUserProvider.
 *
 * @see HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider
 */
class OauthUserProvider implements UserProviderInterface, AccountConnectorInterface, OAuthAwareUserProviderInterface
{
    protected UserManagerInterface $userManager;

    protected array $properties = [
        'identifier' => 'id',
    ];

    protected PropertyAccessor $accessor;

    protected array $oauthServices;

    public function __construct(UserManagerInterface $userManager, array $properties, array $oauthServices)
    {
        $this->userManager = $userManager;
        $this->properties = array_merge($this->properties, $properties);
        $this->oauthServices = $oauthServices;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userManager->findUserByIdentifier($identifier);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * @deprecated this method will be removed on SF 6
     */
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername(); // provides id (identifier)

        $user = $this->userManager->findUserBy([$this->getProperty($response) => $username]);
        if (null === $user || !$username) {
            $user = $this->userManager->findUserByIdentifier($response->getEmail());
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

    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        if (!$user instanceof SoftspringUserInterface) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', SoftspringUserInterface::class, get_class($user)));
        }

        $property = $this->getProperty($response);
        $username = $response->getUsername();

        if (null !== $previousUser = $this->userManager->findUserBy([$property => $username])) {
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
     */
    public function disconnect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);

        $this->accessor->setValue($user, $property, null);
        $this->userManager->saveEntity($user);
    }

    public function refreshUser(UserInterface $user)
    {
        $identifier = $this->properties['identifier'];
        if (!$user instanceof SoftspringUserInterface || !$this->accessor->isReadable($user, $identifier)) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', SoftspringUserInterface::class, get_class($user)));
        }

        $userId = $this->accessor->getValue($user, $identifier);
        if (null === $user = $this->userManager->findUserBy([$identifier => $userId])) {
            throw new UserNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $userId));
        }

        return $user;
    }

    public function supportsClass($class)
    {
        $userClass = $this->userManager->getEntityClass();

        return $userClass === $class || is_subclass_of($class, $userClass);
    }

    /**
     * Gets the property for the response.
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
     * @throws \Exception
     */
    protected function createUser(UserResponseInterface $response): SoftspringUserInterface
    {
        /** @var SoftspringUserInterface $user */
        $user = $this->userManager->createEntity();
        //        $user->setLastLogin(new \DateTime());

        if ($user instanceof EnablableInterface) {
            $user->setEnabled(true);
        }

        if ($user instanceof UserIdentifierUsernameInterface) {
            $user->setUsername($response->getUsername());
        }

        if ($user instanceof UserPasswordInterface) {
            $user->setPassword('invalid-oauth');
        }

        if ($user instanceof RolesInterface) {
            $user->setRoles(['ROLE_USER']);
        }

        if ($user instanceof UserWithEmailInterface) {
            $user->setEmail($response->getEmail());
        }

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
