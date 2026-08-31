<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\RequestMatcher;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * Checks the HTTP method of a Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MethodRequestMatcher implements RequestMatcherInterface
{
    /**
     * @var string[]
     */
    private array $methods = [];

    /**
     * @param string[]|string $methods An HTTP method or an array of HTTP methods
     *                                 Strings can contain a comma-delimited list of methods
     */
    public function __construct(array|string $methods)
    {
        $this->methods = array_reduce(array_map('strtoupper', (array) $methods), static fn (array $methods, string $method) => array_merge($methods, preg_split('/\s*,\s*/', $method)), []);
    }

    public function matches(Request $request): bool
    {
        if (!$this->methods) {
            return true;
        }

        return \in_array($request->getMethod(), $this->methods, true);
    }
}
