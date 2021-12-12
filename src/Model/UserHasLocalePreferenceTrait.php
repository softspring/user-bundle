<?php

namespace Softspring\UserBundle\Model;

trait UserHasLocalePreferenceTrait
{
    /**
     * @var string|null
     */
    protected $locale;

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale
     */
    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}