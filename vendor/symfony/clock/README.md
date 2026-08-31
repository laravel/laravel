Clock Component
===============

Symfony Clock decouples applications from the system clock.

Getting Started
---------------

```bash
composer require symfony/clock
```

```php
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\Clock\ClockInterface;

class MyClockSensitiveClass
{
    public function __construct(
        private ClockInterface $clock,
    ) {
        // Only if you need to force a timezone:
        //$this->clock = $clock->withTimeZone('UTC');
    }

    public function doSomething()
    {
        $now = $this->clock->now();
        // [...] do something with $now, which is a \DateTimeImmutable object

        $this->clock->sleep(2.5); // Pause execution for 2.5 seconds
    }
}

$clock = new NativeClock();
$service = new MyClockSensitiveClass($clock);
$service->doSomething();
```

Resources
---------

 * [Documentation](https://symfony.com/doc/current/components/clock.html)
 * [Contributing](https://symfony.com/doc/current/contributing/index.html)
 * [Report issues](https://github.com/symfony/symfony/issues) and
   [send Pull Requests](https://github.com/symfony/symfony/pulls)
   in the [main Symfony repository](https://github.com/symfony/symfony)
