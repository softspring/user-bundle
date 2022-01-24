<?php

namespace Softspring\UserBundle\Model;

interface UserHasLocalePreferenceInterface
{
    public function getLocale(): ?string;

    public function setLocale(?string $locale): void;
}
