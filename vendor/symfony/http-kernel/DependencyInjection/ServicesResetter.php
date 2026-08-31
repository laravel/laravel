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

use ProxyManager\Proxy\LazyLoadingInterface;
use Symfony\Component\VarExporter\LazyObjectInterface;

/**
 * Resets provided services.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final since Symfony 7.2
 */
class ServicesResetter implements ServicesResetterInterface
{
    /**
     * @param \Traversable<string, object>   $resettableServices
     * @param array<string, string|string[]> $resetMethods
     */
    public function __construct(
        private \Traversable $resettableServices,
        private array $resetMethods,
    ) {
    }

    public function reset(): void
    {
        foreach ($this->resettableServices as $id => $service) {
            if ($service instanceof LazyObjectInterface && !$service->isLazyObjectInitialized(true)) {
                continue;
            }

            if ($service instanceof LazyLoadingInterface && !$service->isProxyInitialized()) {
                continue;
            }

            if (\PHP_VERSION_ID >= 80400 && (new \ReflectionClass($service))->isUninitializedLazyObject($service)) {
                continue;
            }

            foreach ((array) $this->resetMethods[$id] as $resetMethod) {
                if ('?' === $resetMethod[0] && !method_exists($service, $resetMethod = substr($resetMethod, 1))) {
                    continue;
                }

                $service->$resetMethod();
            }
        }
    }
}
