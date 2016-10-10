<?php

if (version_compare(PHP_VERSION, '5.4', '<=')) {
    throw new \RuntimeException('PHP 5.4+ is required for this example.');
}

require __DIR__ . '/../vendor/autoload.php';

use Jeremeamia\SuperClosure\SerializableClosure;

$factorial = new SerializableClosure(function ($n) use (&$factorial) {
    return ($n <= 1) ? 1 : $n * $factorial($n - 1);
});

echo $factorial(5) . PHP_EOL;
//> 120

$serialized = serialize($factorial);
$unserialized = unserialize($serialized);

echo $unserialized(5) . PHP_EOL;
//> 120

