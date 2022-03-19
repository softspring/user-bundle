<?php

namespace Softspring\UserBundle\Model;

interface UserIdentifierUsernameInterface
{
    public function setUsername(?string $username): void;

    public function getUsername();
}