<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class RoutingControllerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('routing.loader.attribute.services')) {
            return;
        }

        $resolve = $container->getParameterBag()->resolveValue(...);
        $taggedClasses = [];
        foreach ($this->findAndSortTaggedServices('routing.controller', $container) as $id) {
            $taggedClasses[$resolve($container->getDefinition($id)->getClass())] = true;
        }

        $container->getDefinition('routing.loader.attribute.services')
            ->replaceArgument(0, array_keys($taggedClasses));
    }
}
