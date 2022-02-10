<?php

namespace Softspring\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NameSurnameTrait
{
    /**
     * @ORM\Column(name="name", type="string", nullable=true, length=50)
     */
    protected ?string $name;

    /**
     * @ORM\Column(name="surname", type="string", nullable=true, length=80)
     */
    protected ?string $surname;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
    }
}
