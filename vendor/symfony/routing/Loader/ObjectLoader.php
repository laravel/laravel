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

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\RouteCollection;

/**
 * A route loader that calls a method on an object to load the routes.
 *
 * @author Ryan Weaver <ryan@knpuniversity.com>
 */
abstract class ObjectLoader extends Loader
{
    /**
     * Returns the object that the method will be called on to load routes.
     *
     * For example, if your application uses a service container,
     * the $id may be a service id.
     */
    abstract protected function getObject(string $id): object;

    /**
     * Calls the object method that will load the routes.
     */
    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        if (!preg_match('/^[^\:]+(?:::(?:[^\:]+))?$/', $resource)) {
            throw new \InvalidArgumentException(\sprintf('Invalid resource "%s" passed to the %s route loader: use the format "object_id::method" or "object_id" if your object class has an "__invoke" method.', $resource, \is_string($type) ? '"'.$type.'"' : 'object'));
        }

        $parts = explode('::', $resource);
        $method = $parts[1] ?? '__invoke';

        $loaderObject = $this->getObject($parts[0]);

        if (!\is_callable([$loaderObject, $method])) {
            throw new \BadMethodCallException(\sprintf('Method "%s" not found on "%s" when importing routing resource "%s".', $method, get_debug_type($loaderObject), $resource));
        }

        $routeCollection = $loaderObject->$method($this, $this->env);

        if (!$routeCollection instanceof RouteCollection) {
            $type = get_debug_type($routeCollection);

            throw new \LogicException(\sprintf('The "%s::%s()" method must return a RouteCollection: "%s" returned.', get_debug_type($loaderObject), $method, $type));
        }

        // make the object file tracked so that if it changes, the cache rebuilds
        $this->addClassResource(new \ReflectionClass($loaderObject), $routeCollection);

        return $routeCollection;
    }

    private function addClassResource(\ReflectionClass $class, RouteCollection $collection): void
    {
        do {
            if (is_file($class->getFileName())) {
                $collection->addResource(new FileResource($class->getFileName()));
            }
        } while ($class = $class->getParentClass());
    }
}
