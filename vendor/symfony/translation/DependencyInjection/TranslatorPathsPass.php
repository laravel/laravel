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

use Symfony\Component\DependencyInjection\Compiler\AbstractRecursivePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\TraceableValueResolver;

/**
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class TranslatorPathsPass extends AbstractRecursivePass
{
    protected bool $skipScalars = true;

    private int $level = 0;

    /**
     * @var array<string, bool>
     */
    private array $paths = [];

    /**
     * @var array<int, Definition>
     */
    private array $definitions = [];

    /**
     * @var array<string, array<string, bool>>
     */
    private array $controllers = [];

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('translator')) {
            return;
        }

        foreach ($this->findControllerArguments($container) as $controller => $argument) {
            $id = substr($controller, 0, strpos($controller, ':') ?: \strlen($controller));
            if ($container->hasDefinition($id)) {
                [$locatorRef] = $argument->getValues();
                $this->controllers[(string) $locatorRef][$container->getDefinition($id)->getClass()] = true;
            }
        }

        try {
            parent::process($container);

            $paths = [];
            foreach ($this->paths as $class => $_) {
                if (($r = $container->getReflectionClass($class)) && !$r->isInterface()) {
                    $paths[] = $r->getFileName();
                    foreach ($r->getTraits() as $trait) {
                        $paths[] = $trait->getFileName();
                    }
                }
            }
            if ($paths) {
                if ($container->hasDefinition('console.command.translation_debug')) {
                    $definition = $container->getDefinition('console.command.translation_debug');
                    $definition->replaceArgument(6, array_merge($definition->getArgument(6), $paths));
                }
                if ($container->hasDefinition('console.command.translation_extract')) {
                    $definition = $container->getDefinition('console.command.translation_extract');
                    $definition->replaceArgument(7, array_merge($definition->getArgument(7), $paths));
                }
            }
        } finally {
            $this->level = 0;
            $this->paths = [];
            $this->definitions = [];
        }
    }

    protected function processValue(mixed $value, bool $isRoot = false): mixed
    {
        if ($value instanceof Reference) {
            if ('translator' === (string) $value) {
                for ($i = $this->level - 1; $i >= 0; --$i) {
                    $class = $this->definitions[$i]->getClass();

                    if (ServiceLocator::class === $class) {
                        if (!isset($this->controllers[$this->currentId ?? ''])) {
                            continue;
                        }
                        foreach ($this->controllers[$this->currentId ?? ''] as $class => $_) {
                            $this->paths[$class] = true;
                        }
                    } else {
                        $this->paths[$class] = true;
                    }

                    break;
                }
            }

            return $value;
        }

        if ($value instanceof Definition) {
            $this->definitions[$this->level++] = $value;
            $value = parent::processValue($value, $isRoot);
            unset($this->definitions[--$this->level]);

            return $value;
        }

        return parent::processValue($value, $isRoot);
    }

    private function findControllerArguments(ContainerBuilder $container): array
    {
        if (!$container->has('argument_resolver.service')) {
            return [];
        }
        $resolverDef = $container->findDefinition('argument_resolver.service');

        if (TraceableValueResolver::class === $resolverDef->getClass()) {
            $resolverDef = $container->getDefinition($resolverDef->getArgument(0));
        }

        $argument = $resolverDef->getArgument(0);
        if ($argument instanceof Reference) {
            $argument = $container->getDefinition($argument);
        }

        return $argument->getArgument(0);
    }
}
