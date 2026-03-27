<?php

namespace Softspring\UserBundle\Tests\Functional;

class PublicFlowsTest extends AbstractWebTestCase
{
    public function testLoginPageRenders(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form input[name="_username"]');
        self::assertSelectorExists('form input[name="_password"]');
    }

    public function testLoginWithFixtureUserRedirectsToPreferences(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/app/login');

        $client->submit($crawler->filter('form')->form([
            '_username' => 'user@example.com',
            '_password' => '123456',
        ]));

        self::assertResponseRedirects('/app/user/preferences/');

        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Preferences');
    }

    public function testRegisterCreatesUserAndAuthenticatesTheNewAccount(): void
    {
        $client = static::createClient();
        $email = sprintf('register-%s@example.com', uniqid());
        $crawler = $client->request('GET', '/app/register/');

        $client->submit($crawler->filter('form')->form([
            'register_form[name]' => 'New',
            'register_form[surname]' => 'User',
            'register_form[email]' => $email,
            'register_form[plainPassword]' => 'new-password',
            'register_form[acceptConditions]' => '1',
        ]));

        self::assertResponseRedirects('/app/register/welcome');

        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Welcome New', $client->getResponse()->getContent());

        $user = $this->findUserByEmail($email);

        self::assertNotNull($user);
        self::assertNotSame('new-password', $user->getPassword());
        self::assertNotNull($user->getConfirmationToken());
    }

    public function testResetPasswordRequestStoresTokenForExistingUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/app/reset-password/');

        $client->submit($crawler->filter('form')->form([
            'reset_password_request_form[email]' => 'user@example.com',
        ]));

        self::assertResponseRedirects('/app/reset-password/sent');

        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('user@example.com', $client->getResponse()->getContent());

        $user = $this->findUserByEmail('user@example.com');

        self::assertNotNull($user);
        self::assertNotNull($user->getPasswordRequestToken());
        self::assertNotNull($user->getPasswordRequestedAt());
    }

    public function testResetPasswordUpdatesStoredPasswordAndClearsToken(): void
    {
        $client = static::createClient();
        $user = $this->createPersistedUser();
        $crawler = $client->request('GET', '/app/reset-password/');

        $client->submit($crawler->filter('form')->form([
            'reset_password_request_form[email]' => $user->getEmail(),
        ]));

        $this->getEntityManager()->clear();
        $user = $this->findUserByEmail($user->getEmail());

        self::assertNotNull($user);
        self::assertNotNull($user->getPasswordRequestToken());

        $resetCrawler = $client->request('GET', sprintf('/app/reset-password/%s/reset/%s', $user->getId(), $user->getPasswordRequestToken()));

        $client->submit($resetCrawler->filter('form')->form([
            'reset_password_form[plainPassword][first]' => 'updated-password',
            'reset_password_form[plainPassword][second]' => 'updated-password',
        ]));

        self::assertResponseRedirects('/app/reset-password/success');

        $client->followRedirect();

        self::assertResponseIsSuccessful();

        $this->getEntityManager()->clear();
        $reloadedUser = $this->findUserByEmail($user->getEmail());

        self::assertNotNull($reloadedUser);
        self::assertNull($reloadedUser->getPasswordRequestToken());
        self::assertNull($reloadedUser->getPasswordRequestedAt());

        $loginCrawler = $client->request('GET', '/app/login');
        $client->submit($loginCrawler->filter('form')->form([
            '_username' => $reloadedUser->getEmail(),
            '_password' => 'updated-password',
        ]));

        self::assertResponseRedirects('/app/user/preferences/');
    }
}
