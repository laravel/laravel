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

/**
 * Provides an intuitive error message when controller fails because it is not registered as a service.
 *
 * @author Simeon Kolev <simeon.kolev9@gmail.com>
 */
final class NotTaggedControllerValueResolver implements ValueResolverInterface
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

        if (!$this->container->has($controller)) {
            $controller = (false !== $i = strrpos($controller, ':'))
                ? substr($controller, 0, $i).strtolower(substr($controller, $i))
                : $controller.'::__invoke';
        }

        if ($this->container->has($controller)) {
            return [];
        }

        $what = \sprintf('argument $%s of "%s()"', $argument->getName(), $controller);
        $message = \sprintf('Could not resolve %s, maybe you forgot to register the controller as a service or missed tagging it with the "controller.service_arguments"?', $what);

        throw new RuntimeException($message);
    }
}
