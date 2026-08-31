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

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Log\Logger;

/**
 * Registers the default logger if necessary.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class LoggerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(LoggerInterface::class)) {
            $container->setAlias(LoggerInterface::class, 'logger');
        }

        if ($container->has('logger')) {
            return;
        }

        if ($debug = $container->getParameter('kernel.debug')) {
            $debug = $container->hasParameter('kernel.runtime_mode.web')
                ? $container->getParameter('kernel.runtime_mode.web')
                : !\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true);
        }

        $container->register('logger', Logger::class)
            ->setArguments([null, null, null, new Reference(RequestStack::class), $debug]);
    }
}
