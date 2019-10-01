<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['app', 'src', 'tests', 'config']);

return PhpCsFixer\Config::create()
    ->setRules([
                   '@Symfony' => true,
                   'array_syntax' => ['syntax' => 'short'],
                   'concat_space' => ['spacing' => 'one'],
                   'declare_equal_normalize' => ['space' => 'single'],
                   'new_with_braces' => false,
                   'not_operator_with_successor_space' => true,
                   'no_useless_else' => true,
                   'ordered_class_elements' => [
                       'order' => [
                           'use_trait',
                           'constant_public',
                           'constant_protected',
                           'constant_private',
                           'property_public',
                           'property_protected',
                           'property_private',
                           'construct',
                           'destruct',
                           'magic',
                           'phpunit',
                           'method_public',
                           'method_protected',
                           'method_private',
                       ],
                   ],
                   'ordered_imports' => true,
                   'phpdoc_align' => false,
                   'phpdoc_order' => true,
                   'psr0' => false,
                   'psr4' => true,
                   'yoda_style' => false,
               ])
    ->setFinder($finder);
