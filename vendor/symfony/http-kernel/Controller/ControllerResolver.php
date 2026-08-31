<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * This implementation uses the '_controller' request attribute to determine
 * the controller to execute.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class ControllerResolver implements ControllerResolverInterface
{
    private array $allowedControllerTypes = [];
    private array $allowedControllerAttributes = [AsController::class => AsController::class];

    public function __construct(
        private ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * @param array<class-string> $types
     * @param array<class-string> $attributes
     */
    public function allowControllers(array $types = [], array $attributes = []): void
    {
        foreach ($types as $type) {
            $this->allowedControllerTypes[$type] = $type;
        }

        foreach ($attributes as $attribute) {
            $this->allowedControllerAttributes[$attribute] = $attribute;
        }
    }

    /**
     * @throws BadRequestException when the request has attribute "_check_controller_is_allowed" set to true and the controller is not allowed
     */
    public function getController(Request $request): callable|false
    {
        if (!$controller = $request->attributes->get('_controller')) {
            $this->logger?->warning('Unable to look for the controller as the "_controller" parameter is missing.');

            return false;
        }

        if (\is_array($controller)) {
            if (isset($controller[0]) && \is_string($controller[0]) && isset($controller[1])) {
                try {
                    $controller[0] = $this->instantiateController($controller[0]);
                } catch (\Error|\LogicException $e) {
                    if (\is_callable($controller)) {
                        return $this->checkController($request, $controller);
                    }

                    throw $e;
                }
            }

            if (!\is_callable($controller)) {
                throw new \InvalidArgumentException(\sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$this->getControllerError($controller));
            }

            return $this->checkController($request, $controller);
        }

        if (\is_object($controller)) {
            if (!\is_callable($controller)) {
                throw new \InvalidArgumentException(\sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$this->getControllerError($controller));
            }

            return $this->checkController($request, $controller);
        }

        if (\function_exists($controller)) {
            return $this->checkController($request, $controller);
        }

        try {
            $callable = $this->createController($controller);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(\sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$e->getMessage(), 0, $e);
        }

        if (!\is_callable($callable)) {
            throw new \InvalidArgumentException(\sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$this->getControllerError($callable));
        }

        return $this->checkController($request, $callable);
    }

    /**
     * Returns a callable for the given controller.
     *
     * @throws \InvalidArgumentException When the controller cannot be created
     */
    protected function createController(string $controller): callable
    {
        if (!str_contains($controller, '::')) {
            $controller = $this->instantiateController($controller);

            if (!\is_callable($controller)) {
                throw new \InvalidArgumentException($this->getControllerError($controller));
            }

            return $controller;
        }

        [$class, $method] = explode('::', $controller, 2);

        try {
            $controller = [$this->instantiateController($class), $method];
        } catch (\Error|\LogicException $e) {
            try {
                if ((new \ReflectionMethod($class, $method))->isStatic()) {
                    return $class.'::'.$method;
                }
            } catch (\ReflectionException) {
                throw $e;
            }

            throw $e;
        }

        if (!\is_callable($controller)) {
            throw new \InvalidArgumentException($this->getControllerError($controller));
        }

        return $controller;
    }

    /**
     * Returns an instantiated controller.
     */
    protected function instantiateController(string $class): object
    {
        return new $class();
    }

    private function getControllerError(mixed $callable): string
    {
        if (\is_string($callable)) {
            if (str_contains($callable, '::')) {
                $callable = explode('::', $callable, 2);
            } else {
                return \sprintf('Function "%s" does not exist.', $callable);
            }
        }

        if (\is_object($callable)) {
            $availableMethods = $this->getClassMethodsWithoutMagicMethods($callable);
            $alternativeMsg = $availableMethods ? \sprintf(' or use one of the available methods: "%s"', implode('", "', $availableMethods)) : '';

            return \sprintf('Controller class "%s" cannot be called without a method name. You need to implement "__invoke"%s.', get_debug_type($callable), $alternativeMsg);
        }

        if (!\is_array($callable)) {
            return \sprintf('Invalid type for controller given, expected string, array or object, got "%s".', get_debug_type($callable));
        }

        if (!isset($callable[0]) || !isset($callable[1]) || 2 !== \count($callable)) {
            return 'Invalid array callable, expected [controller, method].';
        }

        [$controller, $method] = $callable;

        if (\is_string($controller) && !class_exists($controller)) {
            return \sprintf('Class "%s" does not exist.', $controller);
        }

        $className = \is_object($controller) ? get_debug_type($controller) : $controller;

        if (method_exists($controller, $method)) {
            return \sprintf('Method "%s" on class "%s" should be public and non-abstract.', $method, $className);
        }

        $collection = $this->getClassMethodsWithoutMagicMethods($controller);

        $alternatives = [];

        foreach ($collection as $item) {
            $lev = levenshtein($method, $item);

            if ($lev <= \strlen($method) / 3 || str_contains($item, $method)) {
                $alternatives[] = $item;
            }
        }

        asort($alternatives);

        $message = \sprintf('Expected method "%s" on class "%s"', $method, $className);

        if (\count($alternatives) > 0) {
            $message .= \sprintf(', did you mean "%s"?', implode('", "', $alternatives));
        } else {
            $message .= \sprintf('. Available methods: "%s".', implode('", "', $collection));
        }

        return $message;
    }

    private function getClassMethodsWithoutMagicMethods($classOrObject): array
    {
        $methods = get_class_methods($classOrObject);

        return array_filter($methods, static fn (string $method) => 0 !== strncmp($method, '__', 2));
    }

    private function checkController(Request $request, callable $controller): callable
    {
        if (!$request->attributes->get('_check_controller_is_allowed', false)) {
            return $controller;
        }

        $r = null;

        if (\is_array($controller)) {
            [$class, $name] = $controller;
            $name = (\is_string($class) ? $class : $class::class).'::'.$name;
        } elseif (\is_object($controller) && !$controller instanceof \Closure) {
            $class = $controller;
            $name = $class::class.'::__invoke';
        } else {
            $r = new \ReflectionFunction($controller);
            $name = $r->name;

            if ($r->isAnonymous()) {
                $name = $class = \Closure::class;
            } elseif ($class = $r->getClosureCalledClass()) {
                $class = $class->name;
                $name = $class.'::'.$name;
            }
        }

        if ($class) {
            foreach ($this->allowedControllerTypes as $type) {
                if (is_a($class, $type, true)) {
                    return $controller;
                }
            }
        }

        $r ??= new \ReflectionClass($class);

        foreach ($r->getAttributes() as $attribute) {
            if (isset($this->allowedControllerAttributes[$attribute->getName()])) {
                return $controller;
            }
        }

        if (str_contains($name, '@anonymous')) {
            $name = preg_replace_callback('/[a-zA-Z_\x7f-\xff][\\\\a-zA-Z0-9_\x7f-\xff]*+@anonymous\x00.*?\.php(?:0x?|:[0-9]++\$)?[0-9a-fA-F]++/', static fn ($m) => class_exists($m[0], false) ? (get_parent_class($m[0]) ?: key(class_implements($m[0])) ?: 'class').'@anonymous' : $m[0], $name);
        }

        throw new BadRequestException(\sprintf('Callable "%s()" is not allowed as a controller. Did you miss tagging it with "#[AsController]" or registering its type with "%s::allowControllers()"?', $name, self::class));
    }
}
