<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TranslatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('translator.default')) {
            return;
        }

        $loaders = [];
        $loaderRefs = [];
        foreach ($container->findTaggedServiceIds('translation.loader', true) as $id => $attributes) {
            $loaderRefs[$id] = new Reference($id);
            $loaders[$id][] = $attributes[0]['alias'];
            if (isset($attributes[0]['legacy-alias'])) {
                $loaders[$id][] = $attributes[0]['legacy-alias'];
            }
        }

        if ($container->hasDefinition('translation.reader')) {
            $definition = $container->getDefinition('translation.reader');
            foreach ($loaders as $id => $formats) {
                foreach ($formats as $format) {
                    $definition->addMethodCall('addLoader', [$format, $loaderRefs[$id]]);
                }
            }
        }

        $container
            ->findDefinition('translator.default')
            ->replaceArgument(0, ServiceLocatorTagPass::register($container, $loaderRefs))
            ->replaceArgument(3, $loaders)
        ;

        if ($container->hasDefinition('validator') && $container->hasDefinition('translation.extractor.visitor.constraint')) {
            $constraintVisitorDefinition = $container->getDefinition('translation.extractor.visitor.constraint');
            $constraintClassNames = [];

            foreach ($container->getDefinitions() as $definition) {
                if (!$definition->hasTag('validator.constraint_validator')) {
                    continue;
                }
                // Resolve constraint validator FQCN even if defined as %foo.validator.class% parameter
                $className = $container->getParameterBag()->resolveValue($definition->getClass());
                // Extraction of the constraint class name from the Constraint Validator FQCN
                $constraintClassNames[] = str_replace('Validator', '', substr(strrchr($className, '\\'), 1));
            }

            $constraintVisitorDefinition->setArgument(0, $constraintClassNames);
        }

        if (!$container->hasParameter('twig.default_path')) {
            return;
        }

        $paths = array_keys($container->getDefinition('twig.template_iterator')->getArgument(1));
        if ($container->hasDefinition('console.command.translation_debug')) {
            $definition = $container->getDefinition('console.command.translation_debug');
            $definition->replaceArgument(4, $container->getParameter('twig.default_path'));

            if (\count($definition->getArguments()) > 6) {
                $definition->replaceArgument(6, $paths);
            }
        }
        if ($container->hasDefinition('console.command.translation_extract')) {
            $definition = $container->getDefinition('console.command.translation_extract');
            $definition->replaceArgument(5, $container->getParameter('twig.default_path'));

            if (\count($definition->getArguments()) > 7) {
                $definition->replaceArgument(7, $paths);
            }
        }
    }
}
