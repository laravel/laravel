<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\CacheClearer;

/**
 * ChainCacheClearer.
 *
 * @author Dustin Dobervich <ddobervich@gmail.com>
 *
 * @final
 */
class ChainCacheClearer implements CacheClearerInterface
{
    /**
     * @param iterable<mixed, CacheClearerInterface> $clearers
     */
    public function __construct(
        private iterable $clearers = [],
    ) {
    }

    public function clear(string $cacheDir): void
    {
        foreach ($this->clearers as $clearer) {
            $clearer->clear($cacheDir);
        }
    }
}
