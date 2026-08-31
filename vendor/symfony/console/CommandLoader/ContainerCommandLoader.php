<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\CommandLoader;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Loads commands from a PSR-11 container.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class ContainerCommandLoader implements CommandLoaderInterface
{
    /**
     * @param array $commandMap An array with command names as keys and service ids as values
     */
    public function __construct(
        private ContainerInterface $container,
        private array $commandMap,
    ) {
    }

    public function get(string $name): Command
    {
        if (!$this->has($name)) {
            throw new CommandNotFoundException(\sprintf('Command "%s" does not exist.', $name));
        }

        return $this->container->get($this->commandMap[$name]);
    }

    public function has(string $name): bool
    {
        return isset($this->commandMap[$name]) && $this->container->has($this->commandMap[$name]);
    }

    public function getNames(): array
    {
        return array_keys($this->commandMap);
    }
}
