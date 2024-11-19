<?php

namespace Softspring\UserBundle\Model;

use Softspring\MediaBundle\Model\MediaInterface;

trait UserMediaAvatarTrait
{
    use UserAvatarTrait {
        getAvatarUrl as protected parentGetAvatarUrl;
    }

    protected ?MediaInterface $avatarMedia = null;

    public function getAvatarUrl(array $options = []): string
    {
        if ($this->avatarMedia) {
            $url = $this->avatarMedia->getVersion($options['version'] ?? '_original')?->getPublicUrl();
            if ($url) {
                return $url;
            }
        }

        return $this->parentGetAvatarUrl($options);
    }

    public function getAvatarMedia(): ?MediaInterface
    {
        return $this->avatarMedia;
    }

    public function setAvatarMedia(?MediaInterface $avatarMedia): void
    {
        $this->avatarMedia = $avatarMedia;
    }
}
