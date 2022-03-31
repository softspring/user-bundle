<?php

namespace Softspring\UserBundle\Model;

trait UserAvatarTrait
{
    protected ?string $avatarUrl = null;

    public function getAvatarUrl(array $options = []): string
    {
        if (!$this->avatarUrl) {
            $name = $options['name'] ?? implode(' ', array_slice(explode(' ', $this->getDisplayName()), 0, 2));
            $size = $options['size'] ?? 128;
            $background = $options['background'] ?? 'random';

            return sprintf('https://ui-avatars.com/api/?name=%s&size=%u&background=%s', urlencode($name), $size, $background);
        }

        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): void
    {
        $this->avatarUrl = $avatarUrl;
    }
}
