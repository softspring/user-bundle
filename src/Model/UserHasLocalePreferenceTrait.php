<?php

namespace Softspring\UserBundle\Model;

trait UserHasLocalePreferenceTrait
{
    /**
     * @var string|null
     */
    protected $locale;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
