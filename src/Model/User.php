<?php

namespace Softspring\UserBundle\Model;

abstract class User implements UserInterface
{
    public function __toString(): string
    {
        return "{$this->getId()}";
    }

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

    public function serialize(): ?string
    {
        $data = [];

        foreach ($this->getSerializeFields() as $field) {
            $fieldName = $field['name'];

            switch (true) {
                case 'json' === $field['type']:
                    $data[] = json_encode($this->$fieldName);
                    break;

                case 'string' === $field['type']:
                default:
                    $data[] = $this->$fieldName;
            }
        }

        return serialize($data);
    }

    public function unserialize($data): void
    {
        $data = unserialize($data);

        foreach ($this->getSerializeFields() as $field) {
            $fieldName = $field['name'];

            switch (true) {
                case 'json' === $field['type']:
                    $this->$fieldName = json_decode(array_shift($data), true);
                    break;

                case 'string' === $field['type']:
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

    /**
     * @deprecated this method will be removed on SF 6
     */
    public function getUsername()
    {
        return $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * @deprecated this method will be removed on SF 6
     *
     * @throws \Exception
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * @deprecated this method will be removed on SF 6
     *
     * @throws \Exception
     */
    public function getSalt(): ?string
    {
        return null;
    }
}
