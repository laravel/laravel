<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;

use function class_exists;

class EvalLoader implements Loader
{
    /**
     * Load the given mock definition
     *
     * @return void
     */
    public function load(MockDefinition $definition)
    {
        if (class_exists($definition->getClassName(), false)) {
            return;
        }

        eval('?>' . $definition->getCode());
    }
}
