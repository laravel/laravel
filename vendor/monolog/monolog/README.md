<p align="center"><img src="logo.jpg" alt="Monolog" width="400"></p>

# Monolog - Logging for PHP [![Continuous Integration](https://github.com/Seldaek/monolog/workflows/Continuous%20Integration/badge.svg?branch=main)](https://github.com/Seldaek/monolog/actions)

[![Total Downloads](https://img.shields.io/packagist/dt/monolog/monolog.svg)](https://packagist.org/packages/monolog/monolog)
[![Latest Stable Version](https://img.shields.io/packagist/v/monolog/monolog.svg)](https://packagist.org/packages/monolog/monolog)

>**Note** This is the **documentation for Monolog 3.x**, if you are using older releases
>see the documentation for [Monolog 2.x](https://github.com/Seldaek/monolog/blob/2.x/README.md) or [Monolog 1.x](https://github.com/Seldaek/monolog/blob/1.x/README.md)

Monolog sends your logs to files, sockets, inboxes, databases and various
web services. See the complete list of handlers below. Special handlers
allow you to build advanced logging strategies.

This library implements the [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
interface that you can type-hint against in your own libraries to keep
a maximum of interoperability. You can also use it in your applications to
make sure you can always use another compatible logger at a later time.
As of 1.11.0 Monolog public APIs will also accept PSR-3 log levels.
Internally Monolog still uses its own level scheme since it predates PSR-3.

<div align="center">
  <hr>
  <sup><b>Sponsored by:</b></sup>
  <br>
  <a href="https://betterstack.com">
    <div>
      <img src="https://github.com/Seldaek/monolog/assets/183678/7de58ce0-2fa2-45c0-b3e8-e60cebb3c4cf" width="200" alt="Better Stack">
    </div>
    <div>
      Better Stack lets you centralize, search, and visualize your logs.
    </div>
  </a>
  <br>
  <hr>
</div>

## Installation

Install the latest version with

```bash
composer require monolog/monolog
```

## Basic Usage

```php
<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler('path/to/your.log', Level::Warning));

// add records to the log
$log->warning('Foo');
$log->error('Bar');
```

## Documentation

- [Usage Instructions](doc/01-usage.md)
- [Handlers, Formatters and Processors](doc/02-handlers-formatters-processors.md)
- [Utility Classes](doc/03-utilities.md)
- [Extending Monolog](doc/04-extending.md)
- [Log Record Structure](doc/message-structure.md)

## Support Monolog Financially

Get supported Monolog and help fund the project with the [Tidelift Subscription](https://tidelift.com/subscription/pkg/packagist-monolog-monolog?utm_source=packagist-monolog-monolog&utm_medium=referral&utm_campaign=enterprise) or via [GitHub sponsorship](https://github.com/sponsors/Seldaek).

Tidelift delivers commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use.

## Third Party Packages

Third party handlers, formatters and processors are
[listed in the wiki](https://github.com/Seldaek/monolog/wiki/Third-Party-Packages). You
can also add your own there if you publish one.

## About

### Requirements

- Monolog `^3.0` works with PHP 8.1 or above.
- Monolog `^2.5` works with PHP 7.2 or above.
- Monolog `^1.25` works with PHP 5.3 up to 8.1, but is not very maintained anymore and will not receive PHP support fixes anymore.

### Support

Monolog 1.x support is somewhat limited at this point and only important fixes will be done. You should migrate to Monolog 2 or 3 where possible to benefit from all the latest features and fixes.

### Submitting bugs and feature requests

Bugs and feature request are tracked on [GitHub](https://github.com/Seldaek/monolog/issues)

### Framework Integrations

- Frameworks and libraries using [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
  can be used very easily with Monolog since it implements the interface.
- [Symfony](http://symfony.com) comes out of the box with Monolog.
- [Laravel](http://laravel.com/) comes out of the box with Monolog.
- [Lumen](http://lumen.laravel.com/) comes out of the box with Monolog.
- [PPI](https://github.com/ppi/framework) comes out of the box with Monolog.
- [CakePHP](http://cakephp.org/) is usable with Monolog via the [cakephp-monolog](https://github.com/jadb/cakephp-monolog) plugin.
- [XOOPS 2.6](http://xoops.org/) comes out of the box with Monolog.
- [Aura.Web_Project](https://github.com/auraphp/Aura.Web_Project) comes out of the box with Monolog.
- [Nette Framework](http://nette.org/en/) is usable with Monolog via the [contributte/monolog](https://github.com/contributte/monolog) or [orisai/nette-monolog](https://github.com/orisai/nette-monolog) extensions.
- [Proton Micro Framework](https://github.com/alexbilbie/Proton) comes out of the box with Monolog.
- [FuelPHP](http://fuelphp.com/) comes out of the box with Monolog.
- [Equip Framework](https://github.com/equip/framework) comes out of the box with Monolog.
- [Yii 2](http://www.yiiframework.com/) is usable with Monolog via the [yii2-monolog](https://github.com/merorafael/yii2-monolog) or [yii2-psr-log-target](https://github.com/samdark/yii2-psr-log-target) plugins.
- [Hawkbit Micro Framework](https://github.com/HawkBitPhp/hawkbit) comes out of the box with Monolog.
- [SilverStripe 4](https://www.silverstripe.org/) comes out of the box with Monolog.
- [Drupal](https://www.drupal.org/) is usable with Monolog via the [monolog](https://www.drupal.org/project/monolog) module.
- [Aimeos ecommerce framework](https://aimeos.org/) is usable with Monolog via the [ai-monolog](https://github.com/aimeos/ai-monolog) extension.
- [Magento](https://magento.com/) comes out of the box with Monolog.
- [Spiral Framework](https://spiral.dev) comes out of the box with Monolog bridge.
- [WebFramework](https://web-framework.com/) comes out of the box with Monolog.

### Author

Jordi Boggiano - <j.boggiano@seld.be> - <http://twitter.com/seldaek><br />
See also the list of [contributors](https://github.com/Seldaek/monolog/contributors) who participated in this project.

### License

Monolog is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

### Acknowledgements

This library is heavily inspired by Python's [Logbook](https://logbook.readthedocs.io/en/stable/)
library, although most concepts have been adjusted to fit to the PHP world.
