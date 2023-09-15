# CHANGELOG

## [v5.1.0](https://github.com/softspring/user-bundle/releases/tag/v5.1.0)

### Upgrading

*Nothing to do on upgrading*

### Commits

- [7eb89aa](https://github.com/softspring/user-bundle/commit/7eb89aa6c5c0bf37e0efb86a45a07d2df1f062df): Upgrade softspring dependencies to ^5.1
- [1c4b5be](https://github.com/softspring/user-bundle/commit/1c4b5bee1a1c60699342001c3d536f8b2c237c56): Add avatarUrl field to forms
- [47910ec](https://github.com/softspring/user-bundle/commit/47910ecab708943997a4b59340a9cc7aed8eb898): Updates for SF6
- [651a91b](https://github.com/softspring/user-bundle/commit/651a91bcd85c5cfa58780aedbfd24e8ece954b9b): Updates for SF6
- [edb8ebf](https://github.com/softspring/user-bundle/commit/edb8ebfb950ea1ad12267722f01d9c2232e45d30): Update trait to avoid collision on phpmd
- [364e90d](https://github.com/softspring/user-bundle/commit/364e90d6f2204c87ff20f4f17e717352cbfd480e): Configure dependabot and phpmd
- [0fe0266](https://github.com/softspring/user-bundle/commit/0fe0266d72eef2782ff295d26013112bcef067fa): Create dependabot.yml
- [bcb60f2](https://github.com/softspring/user-bundle/commit/bcb60f2cf8da02034e7ba0660f638362fb4cc2b4): Fix code style for newer cs fixer versions
- [73b854d](https://github.com/softspring/user-bundle/commit/73b854d2e99849bde4c60e17abe983ccad74c291): Refactor admin permissions
- [36dd48f](https://github.com/softspring/user-bundle/commit/36dd48fc027afb35b18153e77ec3afc997c1f359): Require permissions-bundle
- [01e09b3](https://github.com/softspring/user-bundle/commit/01e09b3bdf763d38b53be90b03f9a7294ccdbf0d): Update changelog for v5.0.6
- [2210a83](https://github.com/softspring/user-bundle/commit/2210a83f6e02fb426fd3b48432bd9f710071f697): Configure new 5.1 development version
- [b52c9d4](https://github.com/softspring/user-bundle/commit/b52c9d4240f3a78073e6ee62c1c26c536f89dbe7): Add 5.1 branch alias to composer.json

### Changes

```
 .github/dependabot.yml                             | 11 ++++
 .github/workflows/php.yml                          |  6 +--
 .github/workflows/phpmd.yml                        | 57 ++++++++++++++++++++
 CHANGELOG.md                                       |  4 --
 README.md                                          |  2 +-
 composer.json                                      | 24 +++++----
 config/security/admin_role_hierarchy.yaml          | 22 ++++----
 .../services/controller/admin_administrators.yaml  |  8 +--
 config/services/controller/admin_users.yaml        |  8 +--
 docs/1_installation.md                             |  4 +-
 src/Command/CreateUserCommand.php                  |  5 +-
 src/Command/InviteUserCommand.php                  |  5 +-
 src/Command/PromoteUserCommand.php                 |  5 +-
 src/Controller/Admin/AdministratorsController.php  | 10 ++--
 src/Controller/Admin/UsersController.php           |  4 +-
 src/Controller/ResetPasswordController.php         | 10 ++--
 src/DataFixtures/UserFixtures.php                  |  2 +-
 .../Compiler/AliasDoctrineEntityManagerPass.php    |  2 +-
 .../Compiler/ResolveDoctrineTargetEntityPass.php   |  2 +-
 src/DependencyInjection/SfsUserExtension.php       |  4 +-
 src/Event/RegisterExceptionEvent.php               |  2 +-
 src/Form/AcceptInvitationForm.php                  |  4 +-
 src/Form/Admin/AccessHistoryListFilterForm.php     |  4 +-
 src/Form/Admin/AdministratorDeleteForm.php         | 60 +++++++++++-----------
 src/Form/Admin/AdministratorListFilterForm.php     |  4 +-
 src/Form/Admin/AdministratorUpdateForm.php         | 10 +++-
 src/Form/Admin/InvitationCreateForm.php            |  4 +-
 src/Form/Admin/InvitationListFilterForm.php        |  4 +-
 src/Form/Admin/UserDeleteForm.php                  | 60 +++++++++++-----------
 src/Form/Admin/UserListFilterForm.php              |  4 +-
 src/Form/Admin/UserUpdateForm.php                  | 24 +++++----
 src/Form/LoginForm.php                             |  4 +-
 src/Form/RegisterForm.php                          |  4 +-
 src/Form/ResetPasswordForm.php                     |  4 +-
 src/Form/ResetPasswordRequestForm.php              |  4 +-
 src/Form/Settings/ChangeEmailForm.php              |  4 +-
 src/Form/Settings/ChangePasswordForm.php           |  4 +-
 src/Form/Settings/ChangeUsernameForm.php           |  4 +-
 src/Form/Settings/PreferencesForm.php              | 10 +++-
 src/Mailer/UserMailer.php                          |  6 +--
 src/Mailer/UserMailerInterface.php                 |  6 +--
 src/Manipulator/UserInvitationManipulator.php      |  2 +-
 src/Mime/ConfirmationEmail.php                     |  4 +-
 src/Mime/InvitationEmail.php                       |  4 +-
 src/Mime/ResetPasswordEmail.php                    |  4 +-
 src/Model/RolesFullTrait.php                       |  6 ++-
 src/Provider/OauthUserProvider.php                 |  2 +-
 .../Voter/AdminAdministratorsActionsVoter.php      |  2 +-
 src/Security/LoginManager.php                      |  6 +--
 templates/admin/administrators/details.html.twig   |  8 +--
 templates/admin/administrators/list-page.html.twig |  2 +-
 templates/admin/users/details.html.twig            |  4 +-
 templates/admin/users/list-page.html.twig          |  2 +-
 translations/sfs_user.en.yaml                      |  2 +
 translations/sfs_user.es.yaml                      |  2 +
 55 files changed, 281 insertions(+), 194 deletions(-)
```

## [v5.0.5](https://github.com/softspring/user-bundle/releases/tag/v5.0.5)

### Upgrading

*Nothing to do on upgrading*

### Commits

- [bce7193](https://github.com/softspring/user-bundle/commit/bce719377025334a9ac79c9ec972b8dca43f5497): Update changelog

### Changes

```
 CHANGELOG.md | 50 +++++++++++++++++++-------------------------------
 1 file changed, 19 insertions(+), 31 deletions(-)
```

## [v5.0.4](https://github.com/softspring/user-bundle/releases/tag/v5.0.4)

*Nothing has changed since last v5.0.3 version*

## [v5.0.3](https://github.com/softspring/user-bundle/releases/tag/v5.0.3)

*Nothing has changed since last v5.0.2 version*

## [v5.0.2](https://github.com/softspring/user-bundle/releases/tag/v5.0.2)

*Nothing has changed since last v5.0.1 version*

## [v5.0.1](https://github.com/softspring/user-bundle/releases/tag/v5.0.1)

*Nothing has changed since last v5.0.0 version*

## [v5.0.0](https://github.com/softspring/user-bundle/releases/tag/v5.0.0)

*Previous versions are not in changelog*
