# User Bundle Features

Functional definition for `softspring/user-bundle`.

## Purpose

- Provide a reusable user layer for Symfony applications.
- Cover common user lifecycle workflows without forcing a fixed application model.
- Offer extension points so projects can adapt behavior and persistence.

## Main Features

- Base user model contracts and composable traits for:
  - identifiers
  - password support
  - enabled status
  - name and surname
  - locale
  - avatar
  - admin/super-admin roles
- User account workflows:
  - login
  - registration
  - email confirmation
  - password reset request and reset confirmation
  - account settings update (email, username, password)
- Administration workflows:
  - users management
  - administrators management
  - invitations
  - access history
- Optional OAuth integration points.
- Optional Google Identity Platform login with Google Sign-In and One Tap.
- Doctrine filters for user/admin visibility.
- Mailers, MIME classes, events, and manipulators for user lifecycle actions.
- Console commands for common user and admin operations.

## Integration And Extension

- Integrates with Symfony Security, Twig, Validator, Translation, and HTTP components.
- Integrates with Doctrine ORM/Doctrine Bundle.
- Integrates with Softspring packages such as:
  - `permissions-bundle`
  - `crudl-controller`
  - `twig-extra-bundle`
  - `doctrine-query-filters`
  - `doctrine-target-entity-resolver`
- Allows custom application entities by implementing bundle interfaces and composing provided traits.
- Supports custom service overrides for controllers, managers, and mailers through Symfony configuration.

## Expected Capabilities

- Must work with the supported dependency matrix of this line, including Symfony `6.4`, `7.x`, and `8.x`.
- Must keep both regular and lowest dependency validation workflows working (`composer test` and `composer test-bc`).
- Must keep user-facing and admin-facing user workflows stable across minor releases in the same line.

## Asset Integration

- When this bundle ships browser assets, applications may consume them through Webpack or AssetMapper if either tool is present.
- The bundle must not require Webpack or AssetMapper as a hard dependency.
