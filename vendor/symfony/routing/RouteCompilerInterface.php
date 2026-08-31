<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing;

/**
 * RouteCompilerInterface is the interface that all RouteCompiler classes must implement.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface RouteCompilerInterface
{
    /**
     * Compiles the current route instance.
     *
     * @throws \LogicException If the Route cannot be compiled because the
     *                         path or host pattern is invalid
     */
    public static function compile(Route $route): CompiledRoute;
}
