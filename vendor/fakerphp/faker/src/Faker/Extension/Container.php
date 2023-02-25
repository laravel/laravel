<?php

declare(strict_types=1);

namespace Faker\Extension;

use Psr\Container\ContainerInterface;

/**
 * A simple implementation of a container.
 *
 * @experimental This class is experimental and does not fall under our BC promise
 */
final class Container implements ContainerInterface
{
    /**
     * @var array<string, callable|object|string>
     */
    private $definitions;

    private $services = [];

    /**
     * Create a container object with a set of definitions. The array value MUST
     * produce an object that implements Extension.
     *
     * @param array<string, callable|object|string> $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @param string $id
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws ContainerException
     * @throws NotInContainerException
     *
     * @return Extension
     */
    public function get($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException(sprintf(
                'First argument of %s::get() must be string',
                self::class
            ));
        }

        if (array_key_exists($id, $this->services)) {
            return $this->services[$id];
        }

        if (!$this->has($id)) {
            throw new NotInContainerException(sprintf(
                'There is not service with id "%s" in the container.',
                $id
            ));
        }

        $definition = $this->definitions[$id];

        if (is_callable($definition)) {
            try {
                $service = $definition();
            } catch (\Throwable $e) {
                throw new ContainerException(
                    sprintf(
                        'Error while invoking callable for "%s"',
                        $id
                    ),
                    0,
                    $e
                );
            }
        } elseif (is_object($definition)) {
            $service = $definition;
        } elseif (is_string($definition)) {
            if (!class_exists($definition)) {
                throw new ContainerException(sprintf(
                    'Could not instantiate class "%s". Class was not found.',
                    $id
                ));
            }

            try {
                $service = new $definition();
            } catch (\Throwable $e) {
                throw new ContainerException(
                    sprintf(
                        'Could not instantiate class "%s"',
                        $id
                    ),
                    0,
                    $e
                );
            }
        } else {
            throw new ContainerException(sprintf(
                'Invalid type for definition with id "%s"',
                $id
            ));
        }

        if (!$service instanceof Extension) {
            throw new \RuntimeException(sprintf(
                'Service resolved for identifier "%s" does not implement the %s" interface.',
                $id,
                Extension::class
            ));
        }

        $this->services[$id] = $service;

        return $service;
    }

    /**
     * @param string $id
     *
     * @throws \InvalidArgumentException
     */
    public function has($id): bool
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException(sprintf(
                'First argument of %s::get() must be string',
                self::class
            ));
        }

        return array_key_exists($id, $this->definitions);
    }
}
