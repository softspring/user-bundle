# User Bundle

[![Latest Stable](https://img.shields.io/packagist/v/softspring/user-bundle?label=stable&style=flat-square)](https://github.com/softspring/user-bundle/releases)
[![Latest Unstable](https://img.shields.io/packagist/v/softspring/user-bundle?label=unstable&style=flat-square&include_prereleases)](https://github.com/softspring/user-bundle/releases)
[![License](https://img.shields.io/packagist/l/softspring/user-bundle?style=flat-square)](https://github.com/softspring/user-bundle/blob/6.0/LICENSE)
[![PHP Version](https://img.shields.io/packagist/dependency-v/softspring/user-bundle/php?style=flat-square)](https://github.com/softspring/user-bundle/blob/6.0/composer.json)
[![Downloads](https://img.shields.io/packagist/dt/softspring/user-bundle?style=flat-square)](https://packagist.org/packages/softspring/user-bundle)
[![CI](https://img.shields.io/github/actions/workflow/status/softspring/user-bundle/ci.yml?branch=6.0&style=flat-square&label=CI)](https://github.com/softspring/user-bundle/actions/workflows/ci.yml)
[![Coverage](https://img.shields.io/codecov/c/github/softspring/user-bundle?branch=6.0&style=flat-square)](https://codecov.io/gh/softspring/user-bundle)

A complete user bundle for Symfony applications, including user authentication flows, settings pages, invitations, and admin user management screens.

## Armonic

This package is part of [Armonic](https://softspring.es/en/armonic).

## Documentation

[Armonic Documentation](https://armonic.softspring.es/latest/bundles/user-bundle)

## Optional Google Identity Platform Login

This bundle can render Google Sign-In on the login page and sync users with Google Identity Platform.

The feature is optional.

### 1. Add the optional fields to your user entity

Use the trait and interface only if you enable this feature.

```php
use Softspring\UserBundle\Entity\GoogleIdentityPlatformTrait;
use Softspring\UserBundle\Model\GoogleIdentityPlatformAwareInterface;

class User extends UserModel implements GoogleIdentityPlatformAwareInterface
{
    use GoogleIdentityPlatformTrait;
}
```

You will usually also want these existing optional contracts:

- `UserIdentifierEmailInterface`
- `NameSurnameInterface`
- `UserLastLoginInterface`
- `UserAvatarInterface`
- `ConfirmableInterface`

### 2. Import the routes you want to expose

Create a dedicated route import in your application:

```yaml
_sfs_user_google_identity_platform:
    resource: '@SfsUserBundle/config/routing/login_google_identity_platform.yaml'
    prefix: /auth/google
```

With that prefix, the callback URL will be `/auth/google/callback`.

### 3. Enable the feature in `sfs_user`

```yaml
sfs_user:
    login:
        google_identity_platform:
            enabled: true
            client_id: '%env(default::GOOGLE_CLIENT_ID)%'
            api_key: '%env(default::IDENTITY_PLATFORM_API_KEY)%'
            tenant_id: '%env(default::IDENTITY_PLATFORM_TENANT_ID)%'
            success_route: app_home
            failure_route: sfs_user_login
```

### 4. Configure Google

The Google OAuth web client must allow:

- your login origin, for example `https://example.test`
- the callback URL from the imported route, for example `https://example.test/auth/google/callback`

The `client_id` must be a Google web client id ending with `.apps.googleusercontent.com`.

### Notes

- The login page only renders the Google widget when the feature is enabled.
- If the routes are not imported, the login page shows a warning instead of rendering a broken button.
- The backend exchange uses `IDENTITY_PLATFORM_API_KEY`.
- `tenant_id` is optional.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

[Report issues](https://github.com/softspring/user-bundle/issues) and [send Pull Requests](https://github.com/softspring/user-bundle/pulls)

## Security

See [SECURITY.md](SECURITY.md).

## License

This package is free and released under the [AGPL-3.0 license](LICENSE).
