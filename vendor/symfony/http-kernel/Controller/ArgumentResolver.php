<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\SessionValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\VariadicValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactoryInterface;
use Symfony\Component\HttpKernel\Exception\NearMissValueResolverException;
use Symfony\Component\HttpKernel\Exception\ResolverNotFoundException;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * Responsible for resolving the arguments passed to an action.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class ArgumentResolver implements ArgumentResolverInterface
{
    private ArgumentMetadataFactoryInterface $argumentMetadataFactory;
    private iterable $argumentValueResolvers;

    /**
     * @param iterable<mixed, ValueResolverInterface> $argumentValueResolvers
     */
    public function __construct(
        ?ArgumentMetadataFactoryInterface $argumentMetadataFactory = null,
        iterable $argumentValueResolvers = [],
        private ?ContainerInterface $namedResolvers = null,
    ) {
        $this->argumentMetadataFactory = $argumentMetadataFactory ?? new ArgumentMetadataFactory();
        $this->argumentValueResolvers = $argumentValueResolvers ?: self::getDefaultArgumentValueResolvers();
    }

    public function getArguments(Request $request, callable $controller, ?\ReflectionFunctionAbstract $reflector = null): array
    {
        $arguments = [];

        foreach ($this->argumentMetadataFactory->createArgumentMetadata($controller, $reflector) as $metadata) {
            $argumentValueResolvers = $this->argumentValueResolvers;
            $disabledResolvers = [];

            if ($this->namedResolvers && $attributes = $metadata->getAttributesOfType(ValueResolver::class, $metadata::IS_INSTANCEOF)) {
                $resolverName = null;
                foreach ($attributes as $attribute) {
                    if ($attribute->disabled) {
                        $disabledResolvers[$attribute->resolver] = true;
                    } elseif ($resolverName) {
                        throw new \LogicException(\sprintf('You can only pin one resolver per argument, but argument "$%s" of "%s()" has more.', $metadata->getName(), $metadata->getControllerName()));
                    } else {
                        $resolverName = $attribute->resolver;
                    }
                }

                if ($resolverName) {
                    if (!$this->namedResolvers->has($resolverName)) {
                        throw new ResolverNotFoundException($resolverName, $this->namedResolvers instanceof ServiceProviderInterface ? array_keys($this->namedResolvers->getProvidedServices()) : []);
                    }

                    $argumentValueResolvers = [
                        $this->namedResolvers->get($resolverName),
                        new RequestAttributeValueResolver(),
                        new DefaultValueResolver(),
                    ];
                }
            }

            $valueResolverExceptions = [];
            foreach ($argumentValueResolvers as $name => $resolver) {
                if (isset($disabledResolvers[\is_int($name) ? $resolver::class : $name])) {
                    continue;
                }

                try {
                    $count = 0;
                    foreach ($resolver->resolve($request, $metadata) as $argument) {
                        ++$count;
                        $arguments[] = $argument;
                    }
                } catch (NearMissValueResolverException $e) {
                    $valueResolverExceptions[] = $e;
                }

                if (1 < $count && !$metadata->isVariadic()) {
                    throw new \InvalidArgumentException(\sprintf('"%s::resolve()" must yield at most one value for non-variadic arguments.', get_debug_type($resolver)));
                }

                if ($count) {
                    // continue to the next controller argument
                    continue 2;
                }
            }

            $reasons = array_map(static fn (NearMissValueResolverException $e) => $e->getMessage(), $valueResolverExceptions);
            if (!$reasons) {
                $reasons[] = 'Either the argument is nullable and no null value has been provided, no default value has been provided or there is a non-optional argument after this one.';
            }

            $reasonCounter = 1;
            if (\count($reasons) > 1) {
                foreach ($reasons as $i => $reason) {
                    $reasons[$i] = $reasonCounter.') '.$reason;
                    ++$reasonCounter;
                }
            }

            throw new \RuntimeException(\sprintf('Controller "%s" requires the "$%s" argument that could not be resolved. '.($reasonCounter > 1 ? 'Possible reasons: ' : '').'%s', $metadata->getControllerName(), $metadata->getName(), implode(' ', $reasons)));
        }

        return $arguments;
    }

    /**
     * @return iterable<int, ValueResolverInterface>
     */
    public static function getDefaultArgumentValueResolvers(): iterable
    {
        return [
            new RequestAttributeValueResolver(),
            new RequestValueResolver(),
            new SessionValueResolver(),
            new DefaultValueResolver(),
            new VariadicValueResolver(),
        ];
    }
}
