# Working rules

- Run `composer test` and `composer test-bc` sequentially before finishing a package review.
- Preserve explicit event subscriber maps during automated maintenance. Applications and unit tests can register subscribers directly without a service container.
