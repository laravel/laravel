<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Mapping\Loader;

use Symfony\Component\Serializer\Exception\MappingException;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;

/**
 * Calls multiple {@link LoaderInterface} instances in a chain.
 *
 * This class accepts multiple instances of LoaderInterface to be passed to the
 * constructor. When {@link loadClassMetadata()} is called, the same method is called
 * in <em>all</em> of these loaders, regardless of whether any of them was
 * successful or not.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class LoaderChain implements LoaderInterface
{
    /**
     * @var LoaderInterface[]
     */
    private $loaders;

    /**
     * Accepts a list of LoaderInterface instances.
     *
     * @param LoaderInterface[] $loaders An array of LoaderInterface instances
     *
     * @throws MappingException If any of the loaders does not implement LoaderInterface
     */
    public function __construct(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (!$loader instanceof LoaderInterface) {
                throw new MappingException(sprintf('Class %s is expected to implement LoaderInterface', get_class($loader)));
            }
        }

        $this->loaders = $loaders;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadataInterface $metadata)
    {
        $success = false;

        foreach ($this->loaders as $loader) {
            $success = $loader->loadClassMetadata($metadata) || $success;
        }

        return $success;
    }
}
