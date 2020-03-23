<?php

namespace Softspring\UserBundle\Model;

/**
 * Class User
 */
abstract class User implements UserInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $username;

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

    use Traits\AdminRolesTrait;

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
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        $data = [
            $this->password,
            $this->salt,
            $this->username,
            $this->id,
        ];

        if ($this instanceof EnablableInterface) {
            $data[] = $this->isEnabled();
        }

        if ($this instanceof UserWithEmailInterface) {
            $data[] = $this->getEmail();
        }

        return serialize($data);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->password,
            $this->salt,
            $this->username,
            $this->id,
        ) = $data;

        $data = array_slice($data, 4);

        if ($this instanceof EnablableInterface) {
            $this->setEnabled(array_unshift($data));
        }

        if ($this instanceof UserWithEmailInterface) {
            $this->setEmail(array_unshift($data));
        }
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->id;
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
}