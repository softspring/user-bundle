<?php

namespace Softspring\UserBundle\Model;

/**
 * Class User
 */
abstract class User implements UserInterface
{
    /**
     * @var string|null
     */
    protected $username;

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
        if ($this instanceof UserAdminRolesInterface) {
            $this->setRoles([]);
        }
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        if ($this instanceof UserPasswordInterface) {
            $this->setPlainPassword(null);
        }
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        $data = [];

        foreach ($this->getSerializeFields() as $field) {
            $fieldName = $field['name'];

            switch ($field['type']) {
                case 'json':
                    $data[] = json_encode($this->$fieldName);
                    break;

                case 'string':
                default:
                    $data[] = $this->$fieldName;
            }
        }

        return serialize($data);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        foreach ($this->getSerializeFields() as $field) {
            $fieldName = $field['name'];

            switch ($field['type']) {
                case 'json':
                    $this->$fieldName = json_decode(array_shift($data), true);
                    break;

                case 'string':
                default:
                    $this->$fieldName = array_shift($data);
            }
        }
    }

    protected function getSerializeFields(): array
    {
        $fields = [];

        if ($this instanceof UserPasswordInterface) {
            $fields[] = ['name' => 'password', 'type' => 'string' ];
            $fields[] = ['name' => 'salt', 'type' => 'string' ];
        }

        $fields[] = ['name' => 'username', 'type' => 'string' ];

        if ($this instanceof EnablableInterface) {
            $fields[] = ['name' => 'enabled', 'type' => 'string' ];
        }

        if ($this instanceof UserWithEmailInterface) {
            $fields[] = ['name' => 'email', 'type' => 'string' ];
        }

        $reflection = new \ReflectionClass($this);
        if ($reflection->hasProperty('id')) {
            $fields[] = ['name' => 'id', 'type' => $reflection->getProperty('id')->getType() ?? 'string' ];
        }

        return $fields;
    }

    /**
     * @return mixed|null
     */
    abstract public function getId();

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
     * @throws \Exception
     */
    public function getRoles()
    {
        throw new \Exception(sprintf('Please implement %s interface', UserRolesInterface::class));
    }

    /**
     * @throws \Exception
     */
    public function getPassword()
    {
        // nothing to do
    }

    /**
     * @throws \Exception
     */
    public function getSalt()
    {
        // nothing to do
    }
}