<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\DependencyInjection;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LazyCommand;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\TypedReference;

/**
 * Registers console commands.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class AddConsoleCommandPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $commandServices = $container->findTaggedServiceIds('console.command', true);
        $lazyCommandMap = [];
        $lazyCommandRefs = [];
        $serviceIds = [];

        foreach ($commandServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($definition->getClass());

            if (!$r = $container->getReflectionClass($class)) {
                throw new InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }

            if (!$r->isSubclassOf(Command::class)) {
                if (!$r->hasMethod('__invoke')) {
                    throw new InvalidArgumentException(\sprintf('The service "%s" tagged "%s" must either be a subclass of "%s" or have an "__invoke()" method.', $id, 'console.command', Command::class));
                }

                $invokableRef = new Reference($id);
                $definition = $container->register($id .= '.command', $class = Command::class)
                    ->addMethodCall('setCode', [$invokableRef]);
            } else {
                $invokableRef = null;
            }

            $definition->addTag('container.no_preload');

            /** @var AsCommand|null $attribute */
            $attribute = ($r->getAttributes(AsCommand::class)[0] ?? null)?->newInstance();

            if (Command::class !== (new \ReflectionMethod($class, 'getDefaultName'))->class) {
                trigger_deprecation('symfony/console', '7.3', 'Overriding "Command::getDefaultName()" in "%s" is deprecated and will be removed in Symfony 8.0, use the #[AsCommand] attribute instead.', $class);

                $defaultName = $class::getDefaultName();
            } else {
                $defaultName = $attribute?->name;
            }

            $aliases = str_replace('%', '%%', $tags[0]['command'] ?? $defaultName ?? '');
            $aliases = explode('|', $aliases);
            $commandName = array_shift($aliases);

            if ($isHidden = '' === $commandName) {
                $commandName = array_shift($aliases);
            }

            if (null === $commandName) {
                if ($definition->isPrivate() || $definition->hasTag('container.private')) {
                    $commandId = 'console.command.public_alias.'.$id;
                    $container->setAlias($commandId, $id)->setPublic(true);
                    $id = $commandId;
                }
                $serviceIds[] = $id;

                continue;
            }

            $description = $tags[0]['description'] ?? null;
            $help = $tags[0]['help'] ?? null;
            $usages = $tags[0]['usages'] ?? null;

            unset($tags[0]);
            $lazyCommandMap[$commandName] = $id;
            $lazyCommandRefs[$id] = new TypedReference($id, $class);

            foreach ($aliases as $alias) {
                $lazyCommandMap[$alias] = $id;
            }

            foreach ($tags as $tag) {
                if (isset($tag['command'])) {
                    $aliases[] = $tag['command'];
                    $lazyCommandMap[$tag['command']] = $id;
                }

                $description ??= $tag['description'] ?? null;
                $help ??= $tag['help'] ?? null;
                $usages ??= $tag['usages'] ?? null;
            }

            $definition->addMethodCall('setName', [$commandName]);

            if ($aliases) {
                $definition->addMethodCall('setAliases', [$aliases]);
            }

            if ($isHidden) {
                $definition->addMethodCall('setHidden', [true]);
            }

            if ($help && $invokableRef) {
                $definition->addMethodCall('setHelp', [str_replace('%', '%%', $help)]);
            }

            if ($usages) {
                foreach ($usages as $usage) {
                    $definition->addMethodCall('addUsage', [$usage]);
                }
            }

            if (!$description) {
                if (Command::class !== (new \ReflectionMethod($class, 'getDefaultDescription'))->class) {
                    trigger_deprecation('symfony/console', '7.3', 'Overriding "Command::getDefaultDescription()" in "%s" is deprecated and will be removed in Symfony 8.0, use the #[AsCommand] attribute instead.', $class);

                    $description = $class::getDefaultDescription();
                } else {
                    $description = $attribute?->description;
                }
            }

            if ($description) {
                $escapedDescription = str_replace('%', '%%', $description);
                $definition->addMethodCall('setDescription', [$escapedDescription]);

                $container->register('.'.$id.'.lazy', LazyCommand::class)
                    ->setArguments([$commandName, $aliases, $escapedDescription, $isHidden, new ServiceClosureArgument($lazyCommandRefs[$id])]);

                $lazyCommandRefs[$id] = new Reference('.'.$id.'.lazy');
            }
        }

        $container
            ->register('console.command_loader', ContainerCommandLoader::class)
            ->setPublic(true)
            ->addTag('container.no_preload')
            ->setArguments([ServiceLocatorTagPass::register($container, $lazyCommandRefs), $lazyCommandMap]);

        $container->setParameter('console.command.ids', $serviceIds);
    }
}
