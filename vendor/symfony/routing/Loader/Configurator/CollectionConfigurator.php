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

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CollectionConfigurator
{
    use Traits\AddTrait;
    use Traits\HostTrait;
    use Traits\RouteTrait;

    private string|array|null $host = null;

    public function __construct(
        private RouteCollection $parent,
        string $name,
        private ?self $parentConfigurator = null, // for GC control
        private ?array $parentPrefixes = null,
    ) {
        $this->name = $name;
        $this->collection = new RouteCollection();
        $this->route = new Route('');
    }

    public function __serialize(): array
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __unserialize(array $data): void
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        if (null === $this->prefixes) {
            $this->collection->addPrefix($this->route->getPath());
        }
        if (null !== $this->host) {
            $this->addHost($this->collection, $this->host);
        }

        $this->parent->addCollection($this->collection);
    }

    /**
     * Creates a sub-collection.
     */
    final public function collection(string $name = ''): self
    {
        return new self($this->collection, $this->name.$name, $this, $this->prefixes);
    }

    /**
     * Sets the prefix to add to the path of all child routes.
     *
     * @param string|array $prefix the prefix, or the localized prefixes
     *
     * @return $this
     */
    final public function prefix(string|array $prefix): static
    {
        if (\is_array($prefix)) {
            if (null === $this->parentPrefixes) {
                // no-op
            } elseif ($missing = array_diff_key($this->parentPrefixes, $prefix)) {
                throw new \LogicException(\sprintf('Collection "%s" is missing prefixes for locale(s) "%s".', $this->name, implode('", "', array_keys($missing))));
            } else {
                foreach ($prefix as $locale => $localePrefix) {
                    if (!isset($this->parentPrefixes[$locale])) {
                        throw new \LogicException(\sprintf('Collection "%s" with locale "%s" is missing a corresponding prefix in its parent collection.', $this->name, $locale));
                    }

                    $prefix[$locale] = $this->parentPrefixes[$locale].$localePrefix;
                }
            }
            $this->prefixes = $prefix;
            $this->route->setPath('/');
        } else {
            $this->prefixes = null;
            $this->route->setPath($prefix);
        }

        return $this;
    }

    /**
     * Sets the host to use for all child routes.
     *
     * @param string|array $host the host, or the localized hosts
     *
     * @return $this
     */
    final public function host(string|array $host): static
    {
        $this->host = $host;

        return $this;
    }

    /**
     * This method overrides the one from LocalizedRouteTrait.
     */
    private function createRoute(string $path): Route
    {
        return (clone $this->route)->setPath($path);
    }
}
