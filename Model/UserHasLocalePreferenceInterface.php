<?php

namespace Softspring\UserBundle\Model;

interface UserHasLocalePreferenceInterface
{
    /**
     * @return string|null
     */
    public function getLocale(): ?string;

    /**
     * @param string|null $locale
     */
    public function setLocale(?string $locale): void;
}