<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery;
use Mockery\Generator\MockConfiguration;
use function array_reduce;
use function interface_exists;
use function ltrim;
use function str_replace;

class InterfacePass implements Pass
{
    /**
     * @param  string $code
     * @return string
     */
    public function apply($code, MockConfiguration $config)
    {
        foreach ($config->getTargetInterfaces() as $i) {
            $name = ltrim($i->getName(), '\\');
            if (! interface_exists($name)) {
                Mockery::declareInterface($name);
            }
        }

        $interfaces = array_reduce($config->getTargetInterfaces(), static function ($code, $i) {
            return $code . ', \\' . ltrim($i->getName(), '\\');
        }, '');

        return str_replace('implements MockInterface', 'implements MockInterface' . $interfaces, $code);
    }
}
