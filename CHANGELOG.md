# CHANGELOG

## [4.1.0](https://github.com/softspring/user-bundle/releases/tag/4.1.0) (07 mar 2022)

### Upgrading

- This bundle is no longer compatible with old Symfony security system, please consider enable it to keep using this bundle.
- This bundle now uses %kernel.enabled_locales% instead of %sfs_core.locales%, ensure you set its value.
- If you are using OwnerTrait do a migration, because the cascade configuration has been changed.
- Login controller shows the too many login attempts error, check this option.

### Commits

- 3feb960 Remove old security system compatibility
- b0f2f0a Update LoginController to show too many login attempts error
- 48357f7 Merge pull request #1 from softspring/4.0
- bf424dd Remove dev version in composer.json file
- 90e680c Fix property definition
- e951274 Do not cascade in OwnerTrait
- 7ef4ce7 Use framework enabled_locales
- 0f2ab09 Configure new 4.1-dev version to main branch in composer.json file

### Changes

```
 config/services.yaml                 |  2 +-
 src/Controller/LoginController.php   | 13 +++++-
 src/Entity/OwnerTrait.php            |  4 +-
 src/Manager/UserManager.php          | 88 +++++++++---------------------------
 src/Manager/UserManagerInterface.php | 12 ++---
 src/Provider/UserProvider.php        | 15 ++++--
 6 files changed, 53 insertions(+), 81 deletions(-)
```
