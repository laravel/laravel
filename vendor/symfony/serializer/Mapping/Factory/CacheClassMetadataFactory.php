<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Mapping\Factory;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Caches metadata using a PSR-6 implementation.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class CacheClassMetadataFactory implements ClassMetadataFactoryInterface
{
    use ClassResolverTrait;

    /**
     * @var ClassMetadataFactoryInterface
     */
    private $decorated;

    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    public function __construct(ClassMetadataFactoryInterface $decorated, CacheItemPoolInterface $cacheItemPool)
    {
        $this->decorated = $decorated;
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($value)
    {
        $class = $this->getClass($value);
        // Key cannot contain backslashes according to PSR-6
        $key = strtr($class, '\\', '_');

        $item = $this->cacheItemPool->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }

        $metadata = $this->decorated->getMetadataFor($value);
        $this->cacheItemPool->save($item->set($metadata));

        return $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($value)
    {
        return $this->decorated->hasMetadataFor($value);
    }
}
