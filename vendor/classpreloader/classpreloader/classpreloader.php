#! /usr/bin/env php
<?php

if (file_exists($autoloadPath = __DIR__ . '/../../autoload.php')) {
    require_once $autoloadPath;
} else {
    require_once __DIR__ . '/vendor/autoload.php';
}
$application = new ClassPreloader\Application();
$application->run();
