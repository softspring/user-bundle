# Contributing

Run `composer test` to install regular dependencies and validate Composer metadata, PHPStan, unit tests, and code style.
Run `composer test-bc` afterwards to run the same checks with the lowest compatible dependencies.
Run these commands sequentially because both change `composer.lock` and `vendor/`.

The CI runs both workflows on PHP 8.1 and PHP 8.2.
The focused unit suite uses `phpunit-unit.xml.dist` and does not require a database.
The older application test configuration remains separate in `phpunit.xml.dist`.
