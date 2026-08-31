<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NearMissValueResolverException;

/**
 * Yields a service keyed by _controller and argument name.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class ServiceValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        $controller = $request->attributes->get('_controller');

        if (\is_array($controller) && \is_callable($controller, true) && \is_string($controller[0])) {
            $controller = $controller[0].'::'.$controller[1];
        } elseif (!\is_string($controller) || '' === $controller) {
            return [];
        }

        if ('\\' === $controller[0]) {
            $controller = ltrim($controller, '\\');
        }

        if (!$this->container->has($controller) && false !== $i = strrpos($controller, ':')) {
            $controller = substr($controller, 0, $i).strtolower(substr($controller, $i));
        }

        if (!$this->container->has($controller) || !$this->container->get($controller)->has($argument->getName())) {
            return [];
        }

        try {
            return [$this->container->get($controller)->get($argument->getName())];
        } catch (RuntimeException $e) {
            $what = 'argument $'.$argument->getName();
            $message = str_replace(\sprintf('service "%s"', $argument->getName()), $what, $e->getMessage());
            $what .= \sprintf(' of "%s()"', $controller);
            $message = preg_replace('/service "\.service_locator\.[^"]++"/', $what, $message);

            if ($e->getMessage() === $message) {
                $message = \sprintf('Cannot resolve %s: %s', $what, $message);
            }

            throw new NearMissValueResolverException($message, $e->getCode(), $e);
        }
    }
}
