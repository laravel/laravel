<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NearMissValueResolverException;

/**
 * Yields the same instance as the request object passed along.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class RequestValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        if ($request->attributes->has($argument->getName())) {
            return [];
        }

        if (Request::class === $argument->getType() || is_subclass_of($argument->getType(), Request::class)) {
            return [$request];
        }

        if (str_ends_with($argument->getType() ?? '', '\\Request')) {
            throw new NearMissValueResolverException(\sprintf('Looks like you required a Request object with the wrong class name "%s". Did you mean to use "%s" instead?', $argument->getType(), Request::class));
        }

        return [];
    }
}
