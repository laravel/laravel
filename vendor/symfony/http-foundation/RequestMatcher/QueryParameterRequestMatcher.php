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
 * Checks the presence of HTTP query parameters of a Request.
 *
 * @author Alexandre Daubois <alex.daubois@gmail.com>
 */
class QueryParameterRequestMatcher implements RequestMatcherInterface
{
    /**
     * @var string[]
     */
    private array $parameters;

    /**
     * @param string[]|string $parameters A parameter or a list of parameters
     *                                    Strings can contain a comma-delimited list of query parameters
     */
    public function __construct(array|string $parameters)
    {
        $this->parameters = array_reduce(array_map(strtolower(...), (array) $parameters), static fn (array $parameters, string $parameter) => array_merge($parameters, preg_split('/\s*,\s*/', $parameter)), []);
    }

    public function matches(Request $request): bool
    {
        if (!$this->parameters) {
            return true;
        }

        return 0 === \count(array_diff_assoc($this->parameters, $request->query->keys()));
    }
}
