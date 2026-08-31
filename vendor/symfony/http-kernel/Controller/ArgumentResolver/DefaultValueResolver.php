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

/**
 * Yields the default value defined in the action signature when no value has been given.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class DefaultValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        if ($argument->hasDefaultValue()) {
            return [$argument->getDefaultValue()];
        }

        if (null !== $argument->getType() && $argument->isNullable() && !$argument->isVariadic()) {
            return [null];
        }

        return [];
    }
}
