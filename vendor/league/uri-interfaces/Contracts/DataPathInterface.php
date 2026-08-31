<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\Contracts;

use SplFileObject;
use Stringable;

interface DataPathInterface extends PathInterface
{
    /**
     * Retrieve the data mime type associated to the URI.
     *
     * If no mimetype is present, this method MUST return the default mimetype 'text/plain'.
     *
     * @see http://tools.ietf.org/html/rfc2397#section-2
     */
    public function getMimeType(): string;

    /**
     * Retrieve the parameters associated with the Mime Type of the URI.
     *
     * If no parameters is present, this method MUST return the default parameter 'charset=US-ASCII'.
     *
     * @see http://tools.ietf.org/html/rfc2397#section-2
     */
    public function getParameters(): string;

    /**
     * Retrieve the mediatype associated with the URI.
     *
     * If no mediatype is present, this method MUST return the default parameter 'text/plain;charset=US-ASCII'.
     *
     * @see http://tools.ietf.org/html/rfc2397#section-3
     *
     * @return string The URI scheme.
     */
    public function getMediaType(): string;

    /**
     * Retrieves the data string.
     *
     * Retrieves the data part of the path. If no data part is provided return
     * an empty string
     */
    public function getData(): string;

    /**
     * Tells whether the data is binary safe encoded.
     */
    public function isBinaryData(): bool;

    /**
     * Save the data to a specific file.
     */
    public function save(string $path, string $mode = 'w'): SplFileObject;

    /**
     * Returns an instance where the data part is base64 encoded.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance where the data part is base64 encoded
     */
    public function toBinary(): self;

    /**
     * Returns an instance where the data part is url encoded following RFC3986 rules.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance where the data part is url encoded
     */
    public function toAscii(): self;

    /**
     * Return an instance with the specified mediatype parameters.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified mediatype parameters.
     *
     * Users must provide encoded characters.
     *
     * An empty parameters value is equivalent to removing the parameter.
     */
    public function withParameters(Stringable|string $parameters): self;
}
