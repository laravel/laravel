<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use function ltrim;
use function str_replace;

class ClassNamePass implements Pass
{
    /**
     * @param  string $code
     * @return string
     */
    public function apply($code, MockConfiguration $config)
    {
        $namespace = $config->getNamespaceName();

        $namespace = ltrim($namespace, '\\');

        $className = $config->getShortName();

        $code = str_replace('namespace Mockery;', $namespace !== '' ? 'namespace ' . $namespace . ';' : '', $code);

        return str_replace('class Mock', 'class ' . $className, $code);
    }
}
