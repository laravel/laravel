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
 */
class ChainCacheClearer implements CacheClearerInterface
{
    /**
     * @var array $clearers
     */
    protected $clearers;

    /**
     * Constructs a new instance of ChainCacheClearer.
     *
     * @param array $clearers The initial clearers.
     */
    public function __construct(array $clearers = array())
    {
        $this->clearers = $clearers;
    }

    /**
     * {@inheritdoc}
     */
    public function clear($cacheDir)
    {
        foreach ($this->clearers as $clearer) {
            $clearer->clear($cacheDir);
        }
    }

    /**
     * Adds a cache clearer to the aggregate.
     *
     * @param CacheClearerInterface $clearer
     */
    public function add(CacheClearerInterface $clearer)
    {
        $this->clearers[] = $clearer;
    }
}
