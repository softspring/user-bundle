<?php

namespace Softspring\UserBundle\Model;

/**
 * Class UserAccess.
 */
abstract class UserAccess implements UserAccessInterface
{
    protected UserInterface $user;

    protected ?int $loginAt;

    protected string $ip;

    protected string $userAgent;

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getLoginAt(): ?\DateTime
    {
        return \DateTime::createFromFormat('U', $this->loginAt) ?? null;
    }

    public function setLoginAt(?\DateTime $loginAt): void
    {
        $this->loginAt = $loginAt instanceof \DateTime ? $loginAt->format('U') : null;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getOperatingSystemVendor(): ?string
    {
        if (preg_match('/windows/i', $this->getUserAgent())) {
            return 'microsoft';
        }
        if (preg_match('/mac/i', $this->getUserAgent())) {
            return 'apple';
        }
        if (preg_match('/linux/i', $this->getUserAgent())) {
            return 'linux';
        }

        return '';
    }
}
