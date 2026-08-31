<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * GlobFileLoader loads files from a glob pattern.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class GlobFileLoader extends FileLoader
{
    public function load(mixed $resource, ?string $type = null): mixed
    {
        $collection = new RouteCollection();

        foreach ($this->glob($resource, false, $globResource) as $path => $info) {
            $collection->addCollection($this->import($path));
        }

        $collection->addResource($globResource);

        return $collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'glob' === $type;
    }
}
