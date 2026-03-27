<?php

namespace Softspring\UserBundle\Tests\Functional;

class AdminFlowsTest extends AbstractWebTestCase
{
    public function testPreferencesRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/user/preferences/');

        self::assertResponseRedirects('/app/login');
    }

    public function testPreferencesPageRendersForAuthenticatedUser(): void
    {
        $client = static::createClient();
        $user = $this->findUserByEmail('user@example.com');

        self::assertNotNull($user);

        $client->loginUser($user, 'main');
        $client->request('GET', '/app/user/preferences/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Preferences');
    }

    public function testAdminUsersListRendersForAdministrator(): void
    {
        $client = static::createClient();
        $administrator = $this->findUserByEmail('admin@example.com');

        self::assertNotNull($administrator);

        $client->loginUser($administrator, 'main');
        $client->request('GET', '/admin/users/user/');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('user@example.com', $client->getResponse()->getContent());
    }

    public function testAdminAdministratorsListRendersForAdministrator(): void
    {
        $client = static::createClient();
        $administrator = $this->findUserByEmail('admin@example.com');

        self::assertNotNull($administrator);

        $client->loginUser($administrator, 'main');
        $client->request('GET', '/admin/users/admins/');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('admin@example.com', $client->getResponse()->getContent());
    }
}
