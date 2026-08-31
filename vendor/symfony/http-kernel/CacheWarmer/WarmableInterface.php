<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\CacheWarmer;

/**
 * Interface for classes that support warming their cache.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface WarmableInterface
{
    /**
     * Warms up the cache.
     *
     * @param string      $cacheDir Where warm-up artifacts should be stored
     * @param string|null $buildDir Where read-only artifacts should go; null when called after compile-time
     *
     * @return string[] A list of classes or files to preload
     */
    public function warmUp(string $cacheDir, ?string $buildDir = null): array;
}
