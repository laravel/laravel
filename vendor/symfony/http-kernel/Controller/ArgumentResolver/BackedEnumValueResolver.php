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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Attempt to resolve backed enum cases from request attributes, for a route path parameter,
 * leading to a 404 Not Found if the attribute value isn't a valid backing value for the enum type.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
final class BackedEnumValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!is_subclass_of($argument->getType(), \BackedEnum::class)) {
            return [];
        }

        if ($argument->isVariadic()) {
            // only target route path parameters, which cannot be variadic.
            return [];
        }

        // do not support if no value can be resolved at all
        // letting the \Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver be used
        // or \Symfony\Component\HttpKernel\Controller\ArgumentResolver fail with a meaningful error.
        if (!$request->attributes->has($argument->getName())) {
            return [];
        }

        $value = $request->attributes->get($argument->getName());

        if (null === $value) {
            return [null];
        }

        if ($value instanceof \BackedEnum) {
            return [$value];
        }

        if (!\is_int($value) && !\is_string($value)) {
            throw new \LogicException(\sprintf('Could not resolve the "%s $%s" controller argument: expecting an int or string, got "%s".', $argument->getType(), $argument->getName(), get_debug_type($value)));
        }

        /** @var class-string<\BackedEnum> $enumType */
        $enumType = $argument->getType();

        try {
            return [$enumType::from($value)];
        } catch (\ValueError|\TypeError $e) {
            throw new NotFoundHttpException(\sprintf('Could not resolve the "%s $%s" controller argument: ', $argument->getType(), $argument->getName()).$e->getMessage(), $e);
        }
    }
}
