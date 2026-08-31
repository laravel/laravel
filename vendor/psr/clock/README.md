# PSR Clock

This repository holds the interface for [PSR-20][psr-url].

Note that this is not a clock of its own. It is merely an interface that
describes a clock. See the specification for more details.

## Installation

```bash
composer require psr/clock
```

## Usage

If you need a clock, you can use the interface like this:

```php
<?php

use Psr\Clock\ClockInterface;

class Foo
{
    private ClockInterface $clock;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
    }

    public function doSomething()
    {
        /** @var DateTimeImmutable $currentDateAndTime */
        $currentDateAndTime = $this->clock->now();
        // do something useful with that information
    }
}
```

You can then pick one of the [implementations][implementation-url] of the interface to get a clock.

If you want to implement the interface, you can require this package and
implement `Psr\Clock\ClockInterface` in your code. 

Don't forget to add `psr/clock-implementation` to your `composer.json`s `provides`-section like this:

```json
{
  "provides": {
    "psr/clock-implementation": "1.0"
  }
}
```

And please read the [specification text][specification-url] for details on the interface.

[psr-url]: https://www.php-fig.org/psr/psr-20
[package-url]: https://packagist.org/packages/psr/clock
[implementation-url]: https://packagist.org/providers/psr/clock-implementation
[specification-url]: https://github.com/php-fig/fig-standards/blob/master/proposed/clock.md
