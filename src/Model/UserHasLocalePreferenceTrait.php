<?php

namespace Softspring\UserBundle\Model;

trait UserHasLocalePreferenceTrait
{
    protected ?string $locale = null;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
