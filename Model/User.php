<?php

namespace Softspring\UserBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class User
 */
class User implements UserInterface
{
    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $surname;

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var bool
     */
    protected $admin = false;

    /**
     * @var bool
     */
    protected $superAdmin = false;

    /**
     * @var string|null
     */
    protected $salt;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $plainPassword;

    /**
     * @var int|null
     */
    protected $lastLogin;

    /**
     * @var int|null
     */
    protected $passwordRequestedAt;

    /**
     * @var string|null
     */
    protected $passwordRequestToken;

    /**
     * @var string|null
     */
    protected $confirmationToken;

    /**
     * @var int|null
     */
    protected $confirmedAt;

    /**
     * @var array
     */
    protected $roles = [];

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return "{$this->getId()}";
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = [];
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        $roles = $this->roles;

        // we need to make sure to have at least one role
        $roles[] = 'ROLE_USER';

        if ($this->isAdmin()) {
            $roles[] = 'ROLE_ADMIN';
        }

        if ($this->isSuperAdmin()) {
            $roles[] = 'ROLE_SUPER_ADMIN';
        }

        return array_unique($roles);
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            $this->password,
            $this->salt,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->name,
            $this->surname,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list(
            $this->password,
            $this->salt,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->name,
            $this->surname,
        ) = unserialize($serialized);
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname
     */
    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * @param bool $admin
     */
    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->superAdmin;
    }

    /**
     * @param bool $superAdmin
     */
    public function setSuperAdmin(bool $superAdmin): void
    {
        $this->superAdmin = $superAdmin;

        if ($superAdmin) {
            $this->setAdmin(true);
        }
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string|null $salt
     */
    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLogin(): ?\DateTime
    {
        return \DateTime::createFromFormat("U", $this->lastLogin) ?: null;
    }

    /**
     * @param \DateTime|null $lastLogin
     */
    public function setLastLogin(?\DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin instanceof \DateTime ? $lastLogin->format('U') : null;
    }

    /**
     * @return \DateTime|null
     */
    public function getPasswordRequestedAt(): ?\DateTime
    {
        return \DateTime::createFromFormat("U", $this->passwordRequestedAt) ?: null;
    }

    /**
     * @param \DateTime|null $passwordRequestedAt
     */
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt instanceof \DateTime ? $passwordRequestedAt->format('U') : null;
    }

    /**
     * @return string|null
     */
    public function getPasswordRequestToken(): ?string
    {
        return $this->passwordRequestToken;
    }

    /**
     * @param string|null $passwordRequestToken
     */
    public function setPasswordRequestToken(?string $passwordRequestToken): void
    {
        $this->passwordRequestToken = $passwordRequestToken;
    }

    /**
     * @return string|null
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string|null $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return \DateTime|null
     */
    public function getConfirmedAt(): ?\DateTime
    {
        return \DateTime::createFromFormat("U", $this->confirmedAt) ?: null;
    }

    /**
     * @param \DateTime|null $confirmedAt
     */
    public function setConfirmedAt(?\DateTime $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt instanceof \DateTime ? $confirmedAt->format('U') : null;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $rolesCollection = new ArrayCollection($roles);

        if ($rolesCollection->contains('ROLE_ADMIN')) {
            $rolesCollection->removeElement('ROLE_ADMIN');
            $this->setAdmin(true);
        }

        if ($rolesCollection->contains('ROLE_SUPER_ADMIN')) {
            $rolesCollection->removeElement('ROLE_SUPER_ADMIN');
            $this->setSuperAdmin(true);
        }

        $this->roles = $rolesCollection->toArray();
    }
}