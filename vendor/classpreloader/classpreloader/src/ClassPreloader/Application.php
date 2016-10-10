<?php

namespace ClassPreloader;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * ClassPreloader application CLI
 */
class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('ClassPreloader');

        // Create a finder to find each non-abstract command in the filesystem
        $finder = new Finder();
        $finder->files()
            ->in(__DIR__ . '/Command')
            ->notName('Abstract*')
            ->name('*.php');

        // Add each command to the CLI
        foreach ($finder as $file) {
            $filename = str_replace('\\', '/', $file->getRealpath());
            $pos = strrpos($filename, '/ClassPreloader/') + strlen('/ClassPreloader/');
            $class = __NAMESPACE__ . '\\'
                . substr(str_replace('/', '\\', substr($filename, $pos)), 0, -4);
            $this->add(new $class());
        }
    }
}
