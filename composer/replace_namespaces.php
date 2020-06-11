<?php

use Symfony\Component\Finder\Finder;

require __DIR__ . '/../vendor/autoload.php';

$files = Finder::create()->files()->in(__DIR__ . '/..')->depth('== 0')->name([
    'composer.json',
    '.env.example',
    '.env',
    'docker-compose.yml',
    'readme.md',
]);

$docker = Finder::create()->files()->in(__DIR__ . '/../docker');

$classes = Finder::create()->files()->in([
    __DIR__ . '/../app',
    __DIR__ . '/../src',
]);

$files = $files
    ->append($docker)
    ->append($classes);

/** @var \Symfony\Component\Finder\SplFileInfo $file */
foreach ($files as $file) {
    $projectName = basename(dirname(__DIR__));
    $namespace = studly_case($projectName);

    file_put_contents(
        $file->getRealPath(),
        str_replace(
            ['project-name', 'ProjectName'],
            [$projectName, $namespace],
            $file->getContents()
        )
    );
}
