<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Allows filtering of a controller callable.
 *
 * You can call getController() to retrieve the current controller. With
 * setController() you can set a new controller that is used in the processing
 * of the request.
 *
 * Controllers should be callables.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ControllerEvent extends KernelEvent
{
    private string|array|object $controller;
    private \ReflectionFunctionAbstract $controllerReflector;
    private array $attributes;

    public function __construct(HttpKernelInterface $kernel, callable $controller, Request $request, ?int $requestType)
    {
        parent::__construct($kernel, $request, $requestType);

        $this->setController($controller);
    }

    public function getController(): callable
    {
        return $this->controller;
    }

    public function getControllerReflector(): \ReflectionFunctionAbstract
    {
        return $this->controllerReflector;
    }

    /**
     * @param array<class-string, list<object>>|null $attributes
     */
    public function setController(callable $controller, ?array $attributes = null): void
    {
        if (null !== $attributes) {
            $this->attributes = $attributes;
        }

        if (isset($this->controller) && ($controller instanceof \Closure ? $controller == $this->controller : $controller === $this->controller)) {
            $this->controller = $controller;

            return;
        }

        if (null === $attributes) {
            unset($this->attributes);
        }

        if (\is_array($controller) && method_exists(...$controller)) {
            $this->controllerReflector = new \ReflectionMethod(...$controller);
        } elseif (\is_string($controller) && str_contains($controller, '::')) {
            $this->controllerReflector = new \ReflectionMethod(...explode('::', $controller, 2));
        } else {
            $this->controllerReflector = new \ReflectionFunction($controller(...));
        }

        $this->controller = $controller;
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|null $className
     *
     * @return ($className is null ? array<class-string, list<object>> : list<T>)
     */
    public function getAttributes(?string $className = null): array
    {
        if (isset($this->attributes)) {
            return null === $className ? $this->attributes : $this->attributes[$className] ?? [];
        }

        if (\is_array($this->controller) && method_exists(...$this->controller)) {
            $class = new \ReflectionClass($this->controller[0]);
        } elseif (\is_string($this->controller) && false !== $i = strpos($this->controller, '::')) {
            $class = new \ReflectionClass(substr($this->controller, 0, $i));
        } else {
            $class = $this->controllerReflector instanceof \ReflectionFunction && $this->controllerReflector->isAnonymous() ? null : $this->controllerReflector->getClosureCalledClass();
        }
        $this->attributes = [];

        foreach (array_merge($class?->getAttributes() ?? [], $this->controllerReflector->getAttributes()) as $attribute) {
            if (class_exists($attribute->getName())) {
                $this->attributes[$attribute->getName()][] = $attribute->newInstance();
            }
        }

        return null === $className ? $this->attributes : $this->attributes[$className] ?? [];
    }
}
