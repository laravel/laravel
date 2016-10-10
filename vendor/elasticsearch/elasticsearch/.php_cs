<?php

function php_cs() {
    $finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('benchmarks')
        ->exclude('docs')
        ->exclude('util')
        ->in(__DIR__);

    return Symfony\CS\Config\Config::create()
        ->setUsingCache(true)
        ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
        ->finder($finder);
}

return php_cs();
