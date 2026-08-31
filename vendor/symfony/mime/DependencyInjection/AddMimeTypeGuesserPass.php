<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers custom mime types guessers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AddMimeTypeGuesserPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('mime_types')) {
            return;
        }
        $definition = $container->findDefinition('mime_types');
        $id = null;
        foreach ($container->findTaggedServiceIds('mime.mime_type_guesser', true) as $id => $attributes) {
            $definition->addMethodCall('registerGuesser', [new Reference($id)]);
        }
        if (null !== $id) {
            $definition->setPublic(true);
        }
    }
}
