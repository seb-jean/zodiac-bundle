# Contributing

Thank you for considering contributing to SebJeanZodiacBundle!

> [!TIP]
> It's a good idea to read [Symfony's Contribution Guide](https://symfony.com/doc/current/contributing/index.html) first: this bundle follows the same conventions.

## Reporting an issue

If you find a bug or have a feature request, please [open an issue](https://github.com/seb-jean/zodiac-bundle/issues).

For a bug, give the versions of PHP, Symfony and the bundle, the date you passed and the sign you expected: most bugs can be reproduced in a single line.

## Contributing to the code

### Forking the repository

[Fork the repository](https://github.com/seb-jean/zodiac-bundle/fork) on GitHub, then:

```shell
$ git clone git@github.com:<USERNAME>/zodiac-bundle.git
$ cd zodiac-bundle
$ git remote add upstream git@github.com:seb-jean/zodiac-bundle.git
```

### Setting up the development environment

- **PHP 8.4 or higher**
- **Composer**

```shell
$ composer install
```

### Running the checks

The source lives in `src/`, the tests in `tests/`. The bundle follows Symfony's coding standards and is analyzed by PHPStan at the max level.

```shell
# Run the test suite
$ vendor/bin/phpunit

# Run static analysis
$ vendor/bin/phpstan analyse

# Fix the coding standards, license headers included
$ vendor/bin/php-cs-fixer fix
```

The continuous integration runs the test suite on PHP 8.4 and 8.5, with the lowest and the highest supported dependencies, and with the next Symfony version.

### Writing code

- Add tests for any change: the code is fully covered by the test suite.
- Update `docs/index.rst` when the behavior changes.
- Don't call deprecated APIs: a deprecation triggered from the bundle's code makes the test suite fail.

### Submitting a pull request

Create a branch from `1.x`, commit your changes and open a pull request against `1.x`. Describe what the change does and why.

## Keeping your fork up to date

To reset your local `1.x` to match upstream:

```shell
$ git checkout 1.x && \
  git fetch upstream && \
  git reset --hard upstream/1.x && \
  git push origin 1.x
```

To rebase a feature branch on top of `upstream/1.x`:

```shell
$ git checkout my-feature-branch && \
  git rebase upstream/1.x && \
  git push -u --force-with-lease origin my-feature-branch
```
