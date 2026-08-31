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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Alexander M. Turek <me@derrabus.de>
 */
class ResettableServicePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('services_resetter')) {
            return;
        }

        $services = $methods = [];

        foreach ($container->findTaggedServiceIds('kernel.reset', true) as $id => $tags) {
            $services[$id] = new Reference($id, ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE);

            foreach ($tags as $attributes) {
                if (!isset($attributes['method'])) {
                    throw new RuntimeException(\sprintf('Tag "kernel.reset" requires the "method" attribute to be set on service "%s".', $id));
                }

                if (!isset($methods[$id])) {
                    $methods[$id] = [];
                }

                if ('ignore' === ($attributes['on_invalid'] ?? null)) {
                    $attributes['method'] = '?'.$attributes['method'];
                }

                $methods[$id][] = $attributes['method'];
            }
        }

        if (!$services) {
            $container->removeAlias('services_resetter');
            $container->removeDefinition('services_resetter');

            return;
        }

        $container->findDefinition('services_resetter')
            ->setArgument(0, new IteratorArgument($services))
            ->setArgument(1, $methods);
    }
}
