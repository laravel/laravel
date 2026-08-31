<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader\Configurator;

use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class RoutingConfigurator
{
    use Traits\AddTrait;

    public function __construct(
        RouteCollection $collection,
        private PhpFileLoader $loader,
        private string $path,
        private string $file,
        private ?string $env = null,
    ) {
        $this->collection = $collection;
    }

    /**
     * @param string|string[]|null $exclude Glob patterns to exclude from the import
     */
    final public function import(string|array $resource, ?string $type = null, bool $ignoreErrors = false, string|array|null $exclude = null): ImportConfigurator
    {
        $this->loader->setCurrentDir(\dirname($this->path));

        $imported = $this->loader->import($resource, $type, $ignoreErrors, $this->file, $exclude) ?: [];
        if (!\is_array($imported)) {
            return new ImportConfigurator($this->collection, $imported);
        }

        $mergedCollection = new RouteCollection();
        foreach ($imported as $subCollection) {
            $mergedCollection->addCollection($subCollection);
        }

        return new ImportConfigurator($this->collection, $mergedCollection);
    }

    final public function collection(string $name = ''): CollectionConfigurator
    {
        return new CollectionConfigurator($this->collection, $name);
    }

    /**
     * Get the current environment to be able to write conditional configuration.
     */
    final public function env(): ?string
    {
        return $this->env;
    }

    final public function withPath(string $path): static
    {
        $clone = clone $this;
        $clone->path = $clone->file = $path;

        return $clone;
    }
}
