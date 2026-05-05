<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Model;

interface UserHasLocalePreferenceInterface
{
    public function getLocale(): ?string;

    public function setLocale(?string $locale): void;
}
