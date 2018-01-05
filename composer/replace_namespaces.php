<?php

require __DIR__ . '/../vendor/autoload.php';

$composer = \Symfony\Component\Finder\Finder::create()->files()->in(__DIR__ . '/..')->depth('== 0')->name('composer.json');
$classes = \Symfony\Component\Finder\Finder::create()->files()->in([
    __DIR__ . '/../app/Http',
    __DIR__ . '/../app/Infrastructure',
    __DIR__ . '/../app/Providers',
    __DIR__ . '/../src/Repositories',
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
