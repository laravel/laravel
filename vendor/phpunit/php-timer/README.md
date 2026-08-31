# phpunit/php-timer

[![Latest Stable Version](https://poser.pugx.org/phpunit/php-timer/v/stable.png)](https://packagist.org/packages/phpunit/php-timer)
[![CI Status](https://github.com/sebastianbergmann/php-timer/workflows/CI/badge.svg)](https://github.com/sebastianbergmann/php-timer/actions)
[![codecov](https://codecov.io/gh/sebastianbergmann/php-timer/branch/main/graph/badge.svg)](https://codecov.io/gh/sebastianbergmann/php-timer)

Utility class for timing things, factored out of PHPUnit into a stand-alone component.

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

```
composer require phpunit/php-timer
```

If you only need this library during development, for instance to run your project's test suite, then you should add it as a development-time dependency:

```
composer require --dev phpunit/php-timer
```

## Usage

### Basic Timing

```php
require __DIR__ . '/vendor/autoload.php';

use SebastianBergmann\Timer\Timer;

$timer = new Timer;

$timer->start();

foreach (\range(0, 100000) as $i) {
    // ...
}

$duration = $timer->stop();

var_dump(get_class($duration));
var_dump($duration->asString());
var_dump($duration->asSeconds());
var_dump($duration->asMilliseconds());
var_dump($duration->asMicroseconds());
var_dump($duration->asNanoseconds());
```

The code above yields the output below:

```
string(32) "SebastianBergmann\Timer\Duration"
string(9) "00:00.002"
float(0.002851062)
float(2.851062)
float(2851.062)
int(2851062)
```

### Resource Consumption

#### Explicit duration

```php
require __DIR__ . '/vendor/autoload.php';

use SebastianBergmann\Timer\ResourceUsageFormatter;
use SebastianBergmann\Timer\Timer;

$timer = new Timer;
$timer->start();

foreach (\range(0, 100000) as $i) {
    // ...
}

print (new ResourceUsageFormatter)->resourceUsage($timer->stop());
```

The code above yields the output below:

```
Time: 00:00.002, Memory: 6.00 MB
```

#### Duration since PHP Startup (using unreliable `$_SERVER['REQUEST_TIME_FLOAT']`)

```php
require __DIR__ . '/vendor/autoload.php';

use SebastianBergmann\Timer\ResourceUsageFormatter;

foreach (\range(0, 100000) as $i) {
    // ...
}

print (new ResourceUsageFormatter)->resourceUsageSinceStartOfRequest();
```

The code above yields the output below:

```
Time: 00:00.002, Memory: 6.00 MB
```
