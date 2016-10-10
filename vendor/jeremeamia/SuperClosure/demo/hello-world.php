<?php

require __DIR__ . '/../vendor/autoload.php';

use Jeremeamia\SuperClosure\SerializableClosure;

$greeting = 'Hello';
$helloWorld = new SerializableClosure(function ($name = 'World') use ($greeting) {
    echo "{$greeting}, {$name}!\n";
});

$helloWorld();
//> Hello, World!
$helloWorld('Jeremy');
//> Hello, Jeremy!

$serialized = serialize($helloWorld);
$unserialized = unserialize($serialized);

$unserialized();
//> Hello, World!
$unserialized('Jeremy');
//> Hello, Jeremy!
