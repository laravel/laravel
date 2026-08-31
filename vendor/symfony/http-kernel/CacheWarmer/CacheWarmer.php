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
 * Abstract cache warmer that knows how to write a file to the cache.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class CacheWarmer implements CacheWarmerInterface
{
    protected function writeCacheFile(string $file, $content): void
    {
        $tmpFile = @tempnam(\dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $file)) {
            @chmod($file, 0o666 & ~umask());

            return;
        }

        throw new \RuntimeException(\sprintf('Failed to write cache file "%s".', $file));
    }
}
