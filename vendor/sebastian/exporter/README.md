[![Latest Stable Version](https://poser.pugx.org/sebastian/exporter/v)](https://packagist.org/packages/sebastian/exporter)
[![CI Status](https://github.com/sebastianbergmann/exporter/workflows/CI/badge.svg)](https://github.com/sebastianbergmann/exporter/actions)
[![codecov](https://codecov.io/gh/sebastianbergmann/exporter/branch/main/graph/badge.svg)](https://codecov.io/gh/sebastianbergmann/exporter)

# sebastian/exporter

This component provides the functionality to export PHP variables for visualization.

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

```
composer require sebastian/exporter
```

If you only need this library during development, for instance to run your project's test suite, then you should add it as a development-time dependency:

```
composer require --dev sebastian/exporter
```

## Usage

Exporting:

```php
<?php
use SebastianBergmann\Exporter\Exporter;

$exporter = new Exporter;

/*
Exception Object &0000000078de0f0d000000002003a261 (
    'message' => ''
    'string' => ''
    'code' => 0
    'file' => '/home/sebastianbergmann/test.php'
    'line' => 34
    'previous' => null
)
*/

print $exporter->export(new Exception);
```

## Data Types

Exporting simple types:

```php
<?php
use SebastianBergmann\Exporter\Exporter;

$exporter = new Exporter;

// 46
print $exporter->export(46);

// 4.0
print $exporter->export(4.0);

// 'hello, world!'
print $exporter->export('hello, world!');

// false
print $exporter->export(false);

// NAN
print $exporter->export(acos(8));

// -INF
print $exporter->export(log(0));

// null
print $exporter->export(null);

// resource(13) of type (stream)
print $exporter->export(fopen('php://stderr', 'w'));

// Binary String: 0x000102030405
print $exporter->export(chr(0) . chr(1) . chr(2) . chr(3) . chr(4) . chr(5));
```

Exporting complex types:

```php
<?php
use SebastianBergmann\Exporter\Exporter;

$exporter = new Exporter;

/*
Array &0 (
    0 => Array &1 (
        0 => 1
        1 => 2
        2 => 3
    )
    1 => Array &2 (
        0 => ''
        1 => 0
        2 => false
    )
)
*/

print $exporter->export(array(array(1,2,3), array("",0,FALSE)));

/*
Array &0 (
    'self' => Array &1 (
        'self' => Array &1
    )
)
*/

$array = array();
$array['self'] = &$array;
print $exporter->export($array);

/*
stdClass Object &0000000003a66dcc0000000025e723e2 (
    'self' => stdClass Object &0000000003a66dcc0000000025e723e2
)
*/

$obj = new stdClass();
$obj->self = $obj;
print $exporter->export($obj);
```

Compact exports:

```php
<?php
use SebastianBergmann\Exporter\Exporter;

$exporter = new Exporter;

// Array ()
print $exporter->shortenedExport(array());

// Array (...)
print $exporter->shortenedExport(array(1,2,3,4,5));

// stdClass Object ()
print $exporter->shortenedExport(new stdClass);

// Exception Object (...)
print $exporter->shortenedExport(new Exception);

// this\nis\na\nsuper\nlong\nstring\nt...\nspace
print $exporter->shortenedExport(
<<<LONG_STRING
this
is
a
super
long
string
that
wraps
a
lot
and
eats
up
a
lot
of
space
LONG_STRING
);
```
