<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Profiler;

use Psr\Container\ContainerInterface;

class ProfilerStateChecker
{
    public function __construct(
        private ContainerInterface $container,
        private bool $defaultEnabled,
    ) {
    }

    public function isProfilerEnabled(): bool
    {
        return $this->container->get('profiler')?->isEnabled() ?? $this->defaultEnabled;
    }

    public function isProfilerDisabled(): bool
    {
        return !$this->isProfilerEnabled();
    }
}
