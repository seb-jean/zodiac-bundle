SebJeanZodiacBundle: zodiac signs for Symfony
=============================================

[![CI](https://github.com/seb-jean/zodiac-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/seb-jean/zodiac-bundle/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/seb-jean/zodiac-bundle)](https://packagist.org/packages/seb-jean/zodiac-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/seb-jean/zodiac-bundle)](https://packagist.org/packages/seb-jean/zodiac-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/seb-jean/zodiac-bundle)](https://packagist.org/packages/seb-jean/zodiac-bundle)
[![License](https://img.shields.io/github/license/seb-jean/zodiac-bundle)](LICENSE)

This bundle gives the western or Chinese zodiac sign of a date in your Symfony
application.

- Provides the signs as PHP enums, with an emoji and a translated name
- Accepts a `DateTimeInterface`, a date string, a `Stringable` or a Unix
  timestamp
- Adds a `ZodiacCalculator` service, and a `zodiac_sign` Twig filter and
  function
- Works with forms, route parameters, the serializer and Doctrine, without
  extra code

It requires PHP 8.4 or higher and Symfony 8.

Documentation
-------------

Read the [documentation](docs/index.rst) to install and use the bundle.

Contributing
------------

Contributions are welcome: read [CONTRIBUTING.md](CONTRIBUTING.md).

License
-------

This bundle is released under the [MIT license](LICENSE).
