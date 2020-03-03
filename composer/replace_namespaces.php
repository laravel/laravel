<?php

use \Symfony\Component\Finder\Finder;

require __DIR__ . '/../vendor/autoload.php';

$composer = Finder::create()->files()->in(__DIR__ . '/..')->depth('== 0')->name('composer.json');
$classes = Finder::create()->files()->in([
    __DIR__ . '/../app',
    __DIR__ . '/../src',
]);

$files = $composer->append($classes);

/** @var \Symfony\Component\Finder\SplFileInfo $file */
foreach ($files as $file) {

    $namespace = studly_case(basename(dirname(__DIR__)));

    file_put_contents(
        $file->getRealPath(),
        str_replace(
            'ProjectName',
            $namespace,
            $file->getContents()
        )
    );
}
