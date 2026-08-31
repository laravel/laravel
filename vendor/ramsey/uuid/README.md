<h1 align="center">ramsey/uuid</h1>

<p align="center">
    <strong>A PHP library for generating and working with UUIDs.</strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/uuid"><img src="http://img.shields.io/badge/source-ramsey/uuid-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/ramsey/uuid"><img src="https://img.shields.io/packagist/v/ramsey/uuid.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/uuid.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/uuid/blob/4.x/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/uuid.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/ramsey/uuid/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/actions/workflow/status/ramsey/uuid/continuous-integration.yml?branch=4.x&logo=github&style=flat-square" alt="Build Status"></a>
    <a href="https://app.codecov.io/gh/ramsey/uuid/branch/4.x"><img src="https://img.shields.io/codecov/c/github/ramsey/uuid/4.x?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
</p>

ramsey/uuid is a PHP library for generating and working with universally unique
identifiers (UUIDs).

This project adheres to a [code of conduct](CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to
uphold this code.

Much inspiration for this library came from the [Java][javauuid] and
[Python][pyuuid] UUID libraries.

## Installation

The preferred method of installation is via [Composer][]. Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require ramsey/uuid
```

## Upgrading to Version 4

See the documentation for a thorough upgrade guide:

* [Upgrading ramsey/uuid Version 3 to 4](https://uuid.ramsey.dev/en/stable/upgrading/3-to-4.html)

## Documentation

Please see <https://uuid.ramsey.dev> for documentation, tips, examples, and
frequently asked questions.

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue in software that is maintained in this repository, please read
[SECURITY.md][] for instructions on submitting a vulnerability report.

## ramsey/uuid for Enterprise

Available as part of the Tidelift Subscription.

The maintainers of ramsey/uuid and thousands of other packages are working with
Tidelift to deliver commercial support and maintenance for the open source
packages you use to build your applications. Save time, reduce risk, and improve
code health, while paying the maintainers of the exact packages you use.
[Learn more.](https://tidelift.com/subscription/pkg/packagist-ramsey-uuid?utm_source=undefined&utm_medium=referral&utm_campaign=enterprise&utm_term=repo)

## Copyright and License

The ramsey/uuid library is copyright © [Ben Ramsey](https://benramsey.com/) and
licensed for use under the MIT License (MIT). Please see [LICENSE][] for more
information.

[rfc4122]: http://tools.ietf.org/html/rfc4122
[conduct]: https://github.com/ramsey/uuid/blob/4.x/CODE_OF_CONDUCT.md
[javauuid]: http://docs.oracle.com/javase/6/docs/api/java/util/UUID.html
[pyuuid]: http://docs.python.org/3/library/uuid.html
[composer]: http://getcomposer.org/
[contributing.md]: https://github.com/ramsey/uuid/blob/4.x/CONTRIBUTING.md
[security.md]: https://github.com/ramsey/uuid/blob/4.x/SECURITY.md
[license]: https://github.com/ramsey/uuid/blob/4.x/LICENSE
