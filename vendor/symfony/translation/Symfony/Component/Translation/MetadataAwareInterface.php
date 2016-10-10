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
 * MetadataAwareInterface.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface MetadataAwareInterface
{
    /**
     * Gets metadata for the given domain and key.
     *
     * Passing an empty domain will return an array with all metadata indexed by
     * domain and then by key. Passing an empty key will return an array with all
     * metadata for the given domain.
     *
     * @param string $key    The key
     * @param string $domain The domain name
     *
     * @return mixed The value that was set or an array with the domains/keys or null
     */
    public function getMetadata($key = '', $domain = 'messages');

    /**
     * Adds metadata to a message domain.
     *
     * @param string $key    The key
     * @param mixed  $value  The value
     * @param string $domain The domain name
     */
    public function setMetadata($key, $value, $domain = 'messages');

    /**
     * Deletes metadata for the given key and domain.
     *
     * Passing an empty domain will delete all metadata. Passing an empty key will
     * delete all metadata for the given domain.
     *
     * @param string $key    The key
     * @param string $domain The domain name
     */
    public function deleteMetadata($key = '', $domain = 'messages');
}
