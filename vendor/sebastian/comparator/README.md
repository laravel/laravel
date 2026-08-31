[![Latest Stable Version](https://poser.pugx.org/sebastian/comparator/v)](https://packagist.org/packages/sebastian/comparator)
[![CI Status](https://github.com/sebastianbergmann/comparator/workflows/CI/badge.svg)](https://github.com/sebastianbergmann/comparator/actions)
[![codecov](https://codecov.io/gh/sebastianbergmann/comparator/branch/main/graph/badge.svg)](https://codecov.io/gh/sebastianbergmann/comparator)

# sebastian/comparator

This component provides the functionality to compare PHP values for equality.

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

```
composer require sebastian/comparator
```

If you only need this library during development, for instance to run your project's test suite, then you should add it as a development-time dependency:

```
composer require --dev sebastian/comparator
```

## Usage

```php
<?php
use SebastianBergmann\Comparator\Factory;
use SebastianBergmann\Comparator\ComparisonFailure;

$date1 = new DateTime('2013-03-29 04:13:35', new DateTimeZone('America/New_York'));
$date2 = new DateTime('2013-03-29 03:13:35', new DateTimeZone('America/Chicago'));

$factory = new Factory;
$comparator = $factory->getComparatorFor($date1, $date2);

try {
    $comparator->assertEquals($date1, $date2);
    print "Dates match";
} catch (ComparisonFailure $failure) {
    print "Dates don't match";
}
```
