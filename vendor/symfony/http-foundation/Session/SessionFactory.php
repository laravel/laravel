<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageFactoryInterface;

// Help opcache.preload discover always-needed symbols
class_exists(Session::class);

/**
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class SessionFactory implements SessionFactoryInterface
{
    private ?\Closure $usageReporter;

    public function __construct(
        private RequestStack $requestStack,
        private SessionStorageFactoryInterface $storageFactory,
        ?callable $usageReporter = null,
    ) {
        $this->usageReporter = null === $usageReporter ? null : $usageReporter(...);
    }

    public function createSession(): SessionInterface
    {
        return new Session($this->storageFactory->createStorage($this->requestStack->getMainRequest()), null, null, $this->usageReporter);
    }
}
