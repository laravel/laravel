<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

/**
 * Lazily loads fragment renderers from the dependency injection container.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LazyLoadingFragmentHandler extends FragmentHandler
{
    /**
     * @var array<string, bool>
     */
    private array $initialized = [];

    public function __construct(
        private ContainerInterface $container,
        RequestStack $requestStack,
        bool $debug = false,
    ) {
        parent::__construct($requestStack, [], $debug);
    }

    public function render(string|ControllerReference $uri, string $renderer = 'inline', array $options = []): ?string
    {
        if (!isset($this->initialized[$renderer]) && $this->container->has($renderer)) {
            $this->addRenderer($this->container->get($renderer));
            $this->initialized[$renderer] = true;
        }

        return parent::render($uri, $renderer, $options);
    }
}
