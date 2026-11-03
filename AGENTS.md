# Working rules

- Run `composer test` and then `composer test-bc` before finishing a package review.
- Run these workflows sequentially because they rewrite the dependency set.
- Keep mailer regression tests in `tests/Unit`, using `phpunit-unit.xml.dist` without application or database setup.
