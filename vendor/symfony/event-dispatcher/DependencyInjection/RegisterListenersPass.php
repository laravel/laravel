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

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Compiler pass to register tagged services for an event dispatcher.
 */
class RegisterListenersPass implements CompilerPassInterface
{
    private array $hotPathEvents = [];
    private array $noPreloadEvents = [];

    /**
     * @return $this
     */
    public function setHotPathEvents(array $hotPathEvents): static
    {
        $this->hotPathEvents = array_flip($hotPathEvents);

        return $this;
    }

    /**
     * @return $this
     */
    public function setNoPreloadEvents(array $noPreloadEvents): static
    {
        $this->noPreloadEvents = array_flip($noPreloadEvents);

        return $this;
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('event_dispatcher') && !$container->hasAlias('event_dispatcher')) {
            return;
        }

        $aliases = [];

        if ($container->hasParameter('event_dispatcher.event_aliases')) {
            $aliases = $container->getParameter('event_dispatcher.event_aliases');
        }

        $globalDispatcherDefinition = $container->findDefinition('event_dispatcher');

        foreach ($container->findTaggedServiceIds('kernel.event_listener', true) as $id => $events) {
            $noPreload = 0;

            $resolvedEvents = [];
            foreach ($events as $event) {
                if (!isset($event['event'])) {
                    if ($container->getDefinition($id)->hasTag('kernel.event_subscriber')) {
                        continue;
                    }

                    $event['method'] ??= '__invoke';
                    $eventNames = $this->getEventFromTypeDeclaration($container, $id, $event['method']);
                } else {
                    $eventNames = [$event['event']];
                }

                foreach ($eventNames as $eventName) {
                    $event['event'] = $aliases[$eventName] ?? $eventName;
                    $resolvedEvents[] = $event;
                }
            }

            foreach ($resolvedEvents as $event) {
                $priority = $event['priority'] ?? 0;

                if (!isset($event['method'])) {
                    $event['method'] = 'on'.preg_replace_callback([
                        '/(?<=\b|_)[a-z]/i',
                        '/[^a-z0-9]/i',
                    ], static fn ($matches) => strtoupper($matches[0]), $event['event']);
                    $event['method'] = preg_replace('/[^a-z0-9]/i', '', $event['method']);

                    if (null !== ($class = $container->getDefinition($id)->getClass()) && ($r = $container->getReflectionClass($class, false)) && !$r->hasMethod($event['method'])) {
                        if (!$r->hasMethod('__invoke')) {
                            throw new InvalidArgumentException(\sprintf('None of the "%s" or "__invoke" methods exist for the service "%s". Please define the "method" attribute on "kernel.event_listener" tags.', $event['method'], $id));
                        }

                        $event['method'] = '__invoke';
                    }
                }

                $dispatcherDefinition = $globalDispatcherDefinition;
                if (isset($event['dispatcher'])) {
                    $dispatcherDefinition = $container->findDefinition($event['dispatcher']);
                }

                $dispatcherDefinition->addMethodCall('addListener', [$event['event'], [new ServiceClosureArgument(new Reference($id)), $event['method']], $priority]);

                if (isset($this->hotPathEvents[$event['event']])) {
                    $container->getDefinition($id)->addTag('container.hot_path');
                } elseif (isset($this->noPreloadEvents[$event['event']])) {
                    ++$noPreload;
                }
            }

            if ($noPreload && \count($events) === $noPreload) {
                $container->getDefinition($id)->addTag('container.no_preload');
            }
        }

        $extractingDispatcher = new ExtractingEventDispatcher();

        foreach ($container->findTaggedServiceIds('kernel.event_subscriber', true) as $id => $tags) {
            $def = $container->getDefinition($id);

            // We must assume that the class value has been correctly filled, even if the service is created by a factory
            $class = $def->getClass();

            if (!$r = $container->getReflectionClass($class)) {
                throw new InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }
            if (!$r->isSubclassOf(EventSubscriberInterface::class)) {
                throw new InvalidArgumentException(\sprintf('Service "%s" must implement interface "%s".', $id, EventSubscriberInterface::class));
            }
            $class = $r->name;

            $dispatcherDefinitions = [];
            foreach ($tags as $attributes) {
                if (!isset($attributes['dispatcher']) || isset($dispatcherDefinitions[$attributes['dispatcher']])) {
                    continue;
                }

                $dispatcherDefinitions[$attributes['dispatcher']] = $container->findDefinition($attributes['dispatcher']);
            }

            if (!$dispatcherDefinitions) {
                $dispatcherDefinitions = [$globalDispatcherDefinition];
            }

            $noPreload = 0;
            ExtractingEventDispatcher::$aliases = $aliases;
            ExtractingEventDispatcher::$subscriber = $class;
            $extractingDispatcher->addSubscriber($extractingDispatcher);
            foreach ($extractingDispatcher->listeners as $args) {
                $args[1] = [new ServiceClosureArgument(new Reference($id)), $args[1]];
                foreach ($dispatcherDefinitions as $dispatcherDefinition) {
                    $dispatcherDefinition->addMethodCall('addListener', $args);
                }

                if (isset($this->hotPathEvents[$args[0]])) {
                    $container->getDefinition($id)->addTag('container.hot_path');
                } elseif (isset($this->noPreloadEvents[$args[0]])) {
                    ++$noPreload;
                }
            }
            if ($noPreload && \count($extractingDispatcher->listeners) === $noPreload) {
                $container->getDefinition($id)->addTag('container.no_preload');
            }
            $extractingDispatcher->listeners = [];
            ExtractingEventDispatcher::$aliases = [];
        }
    }

    /**
     * @return string[]
     */
    private function getEventFromTypeDeclaration(ContainerBuilder $container, string $id, string $method): array
    {
        if (
            null === ($class = $container->getDefinition($id)->getClass())
            || !($r = $container->getReflectionClass($class, false))
            || !$r->hasMethod($method)
            || 1 > ($m = $r->getMethod($method))->getNumberOfParameters()
            || !(($type = $m->getParameters()[0]->getType()) instanceof \ReflectionNamedType || $type instanceof \ReflectionUnionType)
        ) {
            throw new InvalidArgumentException(\sprintf('Service "%s" must define the "event" attribute on "kernel.event_listener" tags.', $id));
        }

        $types = $type instanceof \ReflectionUnionType ? $type->getTypes() : [$type];

        $names = [];
        foreach ($types as $type) {
            if (!$type instanceof \ReflectionNamedType
                || $type->isBuiltin()
                || Event::class === ($name = $type->getName())
            ) {
                continue;
            }

            $names[] = $name;
        }

        if (!$names) {
            throw new InvalidArgumentException(\sprintf('Service "%s" must define the "event" attribute on "kernel.event_listener" tags.', $id));
        }

        return $names;
    }
}

/**
 * @internal
 */
class ExtractingEventDispatcher extends EventDispatcher implements EventSubscriberInterface
{
    public array $listeners = [];

    public static array $aliases = [];
    public static string $subscriber;

    public function addListener(string $eventName, callable|array $listener, int $priority = 0): void
    {
        $this->listeners[] = [$eventName, $listener[1], $priority];
    }

    public static function getSubscribedEvents(): array
    {
        $events = [];

        foreach ([self::$subscriber, 'getSubscribedEvents']() as $eventName => $params) {
            $events[self::$aliases[$eventName] ?? $eventName] = $params;
        }

        return $events;
    }
}
