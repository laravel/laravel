<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Mapping;

/**
 * Stores metadata needed for serializing and deserializing objects of specific class.
 *
 * Primarily, the metadata stores the set of attributes to serialize or deserialize.
 *
 * There may only exist one metadata for each attribute according to its name.
 *
 * @internal
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface ClassMetadataInterface
{
    /**
     * Returns the name of the backing PHP class.
     *
     * @return string The name of the backing class.
     */
    public function getName();

    /**
     * Adds an {@link AttributeMetadataInterface}.
     *
     * @param AttributeMetadataInterface $attributeMetadata
     */
    public function addAttributeMetadata(AttributeMetadataInterface $attributeMetadata);

    /**
     * Gets the list of {@link AttributeMetadataInterface}.
     *
     * @return AttributeMetadataInterface[]
     */
    public function getAttributesMetadata();

    /**
     * Merges a {@link ClassMetadataInterface} in the current one.
     *
     * @param ClassMetadataInterface $classMetadata
     */
    public function merge(ClassMetadataInterface $classMetadata);

    /**
     * Returns a {@link \ReflectionClass} instance for this class.
     *
     * @return \ReflectionClass
     */
    public function getReflectionClass();
}
