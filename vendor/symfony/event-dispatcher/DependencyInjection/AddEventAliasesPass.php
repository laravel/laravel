<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This pass allows bundles to extend the list of event aliases.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 */
class AddEventAliasesPass implements CompilerPassInterface
{
    public function __construct(
        private array $eventAliases,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        $eventAliases = $container->hasParameter('event_dispatcher.event_aliases') ? $container->getParameter('event_dispatcher.event_aliases') : [];

        $container->setParameter(
            'event_dispatcher.event_aliases',
            array_merge($eventAliases, $this->eventAliases)
        );
    }
}
