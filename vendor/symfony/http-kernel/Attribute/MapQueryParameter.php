<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Attribute;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\QueryParameterValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;

/**
 * Can be used to pass a query parameter to a controller argument.
 *
 * @author Ruud Kamphuis <ruud@ticketswap.com>
 * @author Ionut Enache <i.ovidiuenache@yahoo.com>
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class MapQueryParameter extends ValueResolver
{
    /**
     * @see https://php.net/manual/filter.constants for filter, flags and options
     *
     * @param string|null                                         $name     The name of the query parameter; if null, the name of the argument in the controller will be used
     * @param (FILTER_VALIDATE_*)|(FILTER_SANITIZE_*)|null        $filter   The filter to pass to "filter_var()", deduced from the type-hint if null
     * @param int-mask-of<(FILTER_FLAG_*)|FILTER_NULL_ON_FAILURE> $flags
     * @param array{min_range?: int|float, max_range?: int|float, regexp?: string, ...} $options
     * @param class-string<ValueResolverInterface>|string         $resolver The name of the resolver to use
     */
    public function __construct(
        public ?string $name = null,
        public ?int $filter = null,
        public int $flags = 0,
        public array $options = [],
        string $resolver = QueryParameterValueResolver::class,
        public int $validationFailedStatusCode = Response::HTTP_NOT_FOUND,
    ) {
        parent::__construct($resolver);
    }
}
