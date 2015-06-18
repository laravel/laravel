<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude(['vendor'])
    ->in(__DIR__);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        'align_double_arrow',
        '-psr0'
        ])
    ->finder($finder);
