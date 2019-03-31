<?php

namespace Softspring\UserBundle\Model;

/**
 * Class UserAccess
 */
class UserAccess implements UserAccessInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var int|null
     */
    protected $loginAt;

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime|null
     */
    public function getLoginAt(): ?\DateTime
    {
        return \DateTime::createFromFormat("U", $this->loginAt) ?? null;
    }

    /**
     * @param \DateTime|null $loginAt
     */
    public function setLoginAt(?\DateTime $loginAt): void
    {
        $this->loginAt= $loginAt instanceof \DateTime ? $loginAt->format('U') : null;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }
}