<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DependencyInjection;

use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\TraceableValueResolver;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Gathers and configures the argument value resolvers.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
class ControllerArgumentValueResolverPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('argument_resolver')) {
            return;
        }

        $definitions = $container->getDefinitions();
        $namedResolvers = $this->findAndSortTaggedServices(new TaggedIteratorArgument('controller.targeted_value_resolver', 'name', needsIndexes: true), $container);
        $resolvers = $this->findAndSortTaggedServices(new TaggedIteratorArgument('controller.argument_value_resolver', 'name', needsIndexes: true), $container);

        foreach ($resolvers as $name => $resolver) {
            if ($definitions[(string) $resolver]->hasTag('controller.targeted_value_resolver')) {
                unset($resolvers[$name]);
            } else {
                $namedResolvers[$name] ??= clone $resolver;
            }
        }

        if ($container->getParameter('kernel.debug') && class_exists(Stopwatch::class) && $container->has('debug.stopwatch')) {
            foreach ($resolvers as $name => $resolver) {
                $resolvers[$name] = new Reference('.debug.value_resolver.'.$resolver);
                $container->register('.debug.value_resolver.'.$resolver, TraceableValueResolver::class)
                    ->setArguments([$resolver, new Reference('debug.stopwatch')]);
            }
            foreach ($namedResolvers as $name => $resolver) {
                $namedResolvers[$name] = new Reference('.debug.value_resolver.'.$resolver);
                $container->register('.debug.value_resolver.'.$resolver, TraceableValueResolver::class)
                    ->setArguments([$resolver, new Reference('debug.stopwatch')]);
            }
        }

        $container
            ->getDefinition('argument_resolver')
            ->replaceArgument(1, new IteratorArgument(array_values($resolvers)))
            ->setArgument(2, new ServiceLocatorArgument($namedResolvers))
        ;
    }
}
