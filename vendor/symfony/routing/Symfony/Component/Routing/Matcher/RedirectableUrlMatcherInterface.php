<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher;

/**
 * RedirectableUrlMatcherInterface knows how to redirect the user.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
interface RedirectableUrlMatcherInterface
{
    /**
     * Redirects the user to another URL.
     *
     * @param string      $path   The path info to redirect to.
     * @param string      $route  The route name that matched
     * @param string|null $scheme The URL scheme (null to keep the current one)
     *
     * @return array An array of parameters
     *
     * @api
     */
    public function redirect($path, $route, $scheme = null);
}
