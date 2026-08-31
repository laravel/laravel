<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Closure;

use function func_get_args;

/**
 * @internal
 */
class ClosureWrapper
{
    private $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return ($this->closure)(...func_get_args());
    }
}
