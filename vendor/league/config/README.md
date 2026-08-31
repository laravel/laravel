# league/config

[![Latest Version](https://img.shields.io/packagist/v/league/config.svg?style=flat-square)](https://packagist.org/packages/league/config)
[![Total Downloads](https://img.shields.io/packagist/dt/league/config.svg?style=flat-square)](https://packagist.org/packages/league/config)
[![Software License](https://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/workflow/status/thephpleague/config/Tests/main.svg?style=flat-square)](https://github.com/thephpleague/config/actions?query=workflow%3ATests+branch%3Amain)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/config.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/config/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/config.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/config)
[![Sponsor development of this project](https://img.shields.io/badge/sponsor%20this%20package-%E2%9D%A4-ff69b4.svg?style=flat-square)](https://www.colinodell.com/sponsor)

**league/config** helps you define nested configuration arrays with strict schemas and access configuration values with dot notation.  It was created by [Colin O'Dell][@colinodell].

## 📦 Installation

This project requires PHP 7.4 or higher.  To install it via [Composer] simply run:

```bash
composer require league/config
```

## 🧰️ Basic Usage

The `Configuration` class provides everything you need to define the configuration structure and fetch values:

```php
use League\Config\Configuration;
use Nette\Schema\Expect;

// Define your configuration schema
$config = new Configuration([
    'database' => Expect::structure([
        'driver' => Expect::anyOf('mysql', 'postgresql', 'sqlite')->required(),
        'host' => Expect::string()->default('localhost'),
        'port' => Expect::int()->min(1)->max(65535),
        'ssl' => Expect::bool(),
        'database' => Expect::string()->required(),
        'username' => Expect::string()->required(),
        'password' => Expect::string()->nullable(),
    ]),
    'logging' => Expect::structure([
        'enabled' => Expect::bool()->default($_ENV['DEBUG'] == true),
        'file' => Expect::string()->deprecated("use logging.path instead"),
        'path' => Expect::string()->assert(function ($path) { return \is_writeable($path); })->required(),
    ]),
]);

// Set the values, either all at once with `merge()`:
$config->merge([
    'database' => [
        'driver' => 'mysql',
        'port' => 3306,
        'database' => 'mydb',
        'username' => 'user',
        'password' => 'secret',
    ],
]);

// Or one-at-a-time with `set()`:
$config->set('logging.path', '/var/log/myapp.log');

// You can now retrieve those values with `get()`.
// Validation and defaults will be applied for you automatically
$config->get('database');        // Fetches the entire "database" section as an array
$config->get('database.driver'); // Fetch a specific nested value with dot notation
$config->get('database/driver'); // Fetch a specific nested value with slash notation
$config->get('database.host');   // Returns the default value "localhost"
$config->get('logging.path');    // Guaranteed to be writeable thanks to the assertion in the schema

// If validation fails an `InvalidConfigurationException` will be thrown:
$config->set('database.driver', 'mongodb');
$config->get('database.driver'); // InvalidConfigurationException

// Attempting to fetch a non-existent key will result in an `InvalidConfigurationException`
$config->get('foo.bar');

// You could avoid this by checking whether that item exists:
$config->exists('foo.bar'); // Returns `false`
```

## 📓 Documentation

Full documentation can be found at [config.thephpleague.com][docs].

## 💭 Philosophy

This library aims to provide a **simple yet opinionated** approach to configuration with the following goals:

- The configuration should operate on **arrays with nested values** which are easily accessible
- The configuration structure should be **defined with strict schemas** defining the overall structure, allowed types, and allowed values
- Schemas should be defined using a **simple, fluent interface**
- You should be able to **add and combine schemas but never modify existing ones**
- Both the configuration values and the schema should be **defined and managed with PHP code**
- Schemas should be **immutable**; they should never change once they are set
- Configuration values should never define or influence the schemas

As a result, this library will likely **never** support features like:

- Loading and/or exporting configuration values or schemas using YAML, XML, or other files
- Parsing configuration values from a command line or other user interface
- Dynamically changing the schema, allowed values, or default values based on other configuration values

If you need that functionality you should check out other libraries like:

- [symfony/config]
- [symfony/options-resolver]
- [hassankhan/config]
- [consolidation/config]
- [laminas/laminas-config]

## 🏷️ Versioning

[SemVer](http://semver.org/) is followed closely. Minor and patch releases should not introduce breaking changes to the codebase.

Any classes or methods marked `@internal` are not intended for use outside this library and are subject to breaking changes at any time, so please avoid using them.

## 🛠️ Maintenance & Support

When a new **minor** version (e.g. `1.0` -> `1.1`) is released, the previous one (`1.0`) will continue to receive security and critical bug fixes for *at least* 3 months.

When a new **major** version is released (e.g. `1.1` -> `2.0`), the previous one (`1.1`) will receive critical bug fixes for *at least* 3 months and security updates for 6 months after that new release comes out.

(This policy may change in the future and exceptions may be made on a case-by-case basis.)

## 👷‍️ Contributing

Contributions to this library are **welcome**! We only ask that you adhere to our [contributor guidelines] and avoid making changes that conflict with our Philosophy above.

## 🧪 Testing

```bash
composer test
```

## 📄 License

**league/config** is licensed under the BSD-3 license.  See the [`LICENSE.md`][license] file for more details.

## 🗺️  Who Uses It?

This project is used by [league/commonmark][league-commonmark].

[docs]: https://config.thephpleague.com/
[@colinodell]: https://www.twitter.com/colinodell
[Composer]: https://getcomposer.org/
[PHP League]: https://thephpleague.com
[symfony/config]: https://symfony.com/doc/current/components/config.html
[symfony/options-resolver]: https://symfony.com/doc/current/components/options_resolver.html
[hassankhan/config]: https://github.com/hassankhan/config
[consolidation/config]: https://github.com/consolidation/config
[laminas/laminas-config]: https://docs.laminas.dev/laminas-config/
[contributor guidelines]: https://github.com/thephpleague/config/blob/main/.github/CONTRIBUTING.md
[license]: https://github.com/thephpleague/config/blob/main/LICENSE.md
[league-commonmark]: https://commonmark.thephpleague.com
