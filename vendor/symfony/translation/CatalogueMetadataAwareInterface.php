<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation;

/**
 * This interface is used to get, set, and delete metadata about the Catalogue.
 *
 * @author Hugo Alliaume <hugo@alliau.me>
 */
interface CatalogueMetadataAwareInterface
{
    /**
     * Gets catalogue metadata for the given domain and key.
     *
     * Passing an empty domain will return an array with all catalogue metadata indexed by
     * domain and then by key. Passing an empty key will return an array with all
     * catalogue metadata for the given domain.
     *
     * @return mixed The value that was set or an array with the domains/keys or null
     */
    public function getCatalogueMetadata(string $key = '', string $domain = 'messages'): mixed;

    /**
     * Adds catalogue metadata to a message domain.
     */
    public function setCatalogueMetadata(string $key, mixed $value, string $domain = 'messages'): void;

    /**
     * Deletes catalogue metadata for the given key and domain.
     *
     * Passing an empty domain will delete all catalogue metadata. Passing an empty key will
     * delete all metadata for the given domain.
     */
    public function deleteCatalogueMetadata(string $key = '', string $domain = 'messages'): void;
}
