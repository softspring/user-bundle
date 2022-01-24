<?php

namespace Softspring\UserBundle\Model;

interface UserAccessInterface
{
    public function getUser(): UserInterface;

    public function setUser(UserInterface $user): void;

    public function getLoginAt(): ?\DateTime;

    public function setLoginAt(?\DateTime $loginAt): void;

    public function getUserAgent(): string;

    public function setUserAgent(string $userAgent): void;

    public function getIp(): string;

    public function setIp(string $ip): void;
}
