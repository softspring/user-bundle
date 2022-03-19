<?php

namespace Softspring\UserBundle\Model;

/**
 * Class User.
 */
abstract class User implements UserInterface
{
    public function __toString(): string
    {
        return "{$this->getId()}";
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        if ($this instanceof RolesInterface || $this instanceof RolesAdminInterface || $this instanceof RolesFullInterface) {
            $this->setRoles([]);
        }
    }

    public function eraseCredentials()
    {
        if ($this instanceof UserPasswordInterface) {
            $this->setPlainPassword(null);
        }
    }

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

    protected function getIdentifierField(): string
    {
        return 'username';
    }

    protected function getSerializeFields(): array
    {
        $fields = [];

        if ($this instanceof UserPasswordInterface) {
            $fields[] = ['name' => 'password', 'type' => 'string'];
            $fields[] = ['name' => 'salt', 'type' => 'string'];
        }

        $fields[] = ['name' => $this->getIdentifierField(), 'type' => 'string'];

        if ($this instanceof EnablableInterface) {
            $fields[] = ['name' => 'enabled', 'type' => 'string'];
        }

        if ($this instanceof UserWithEmailInterface) {
            $fields[] = ['name' => 'email', 'type' => 'string'];
        }

        $reflection = new \ReflectionClass($this);
        if ($reflection->hasProperty('id')) {
            $fields[] = ['name' => 'id', 'type' => $reflection->getProperty('id')->getType() ?? 'string'];
        }

        return $fields;
    }

    /**
     * @return mixed|null
     */
    abstract public function getId();

    public function getUsername(): ?string
    {
        return $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * @throws \Exception
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * @throws \Exception
     */
    public function getSalt(): ?string
    {
        return null;
    }
}
