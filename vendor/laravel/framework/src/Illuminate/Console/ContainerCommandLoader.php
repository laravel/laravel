<?php

namespace Illuminate\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class ContainerCommandLoader implements CommandLoaderInterface
{
    /**
     * The container instance.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * A map of command names to classes.
     *
     * @var array<string, \Illuminate\Console\Command|string>
     */
    protected $commandMap;

    /**
     * Create a new command loader instance.
     *
     * @param  \Psr\Container\ContainerInterface  $container
     * @param  array<string, \Illuminate\Console\Command|string>  $commandMap
     */
    public function __construct(ContainerInterface $container, array $commandMap)
    {
        $this->container = $container;
        $this->commandMap = $commandMap;
    }

    /**
     * Resolve a command from the container.
     *
     * @param  string  $name
     * @return \Symfony\Component\Console\Command\Command
     *
     * @throws \Symfony\Component\Console\Exception\CommandNotFoundException
     */
    public function get(string $name): Command
    {
        if (! $this->has($name)) {
            throw new CommandNotFoundException(sprintf('Command "%s" does not exist.', $name));
        }

        return $this->container->get($this->commandMap[$name]);
    }

    /**
     * Determines if a command exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return $name && isset($this->commandMap[$name]);
    }

    /**
     * Get the command names.
     *
     * @return string[]
     */
    public function getNames(): array
    {
        return array_keys($this->commandMap);
    }
}
