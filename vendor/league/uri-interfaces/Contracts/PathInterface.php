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

use League\Uri\Exceptions\SyntaxError;

/**
 * @method static normalize() returns the normalized string representation of the component
 */
interface PathInterface extends UriComponentInterface
{
    /**
     * Returns the decoded path.
     */
    public function decoded(): string;

    /**
     * Tells whether the path is absolute or relative.
     */
    public function isAbsolute(): bool;

    /**
     * Tells whether the path has a trailing slash.
     */
    public function hasTrailingSlash(): bool;

    /**
     * Returns an instance without dot segments.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component normalized by removing
     * the dot segment.
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in a object in invalid state.
     */
    public function withoutDotSegments(): self;

    /**
     * Returns an instance with a leading slash.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component with a leading slash
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in a object in invalid state.
     */
    public function withLeadingSlash(): self;

    /**
     * Returns an instance without a leading slash.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component without a leading slash
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in a object in invalid state.
     */
    public function withoutLeadingSlash(): self;

    /**
     * Returns an instance with a trailing slash.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component with a trailing slash
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in a object in invalid state.
     */
    public function withTrailingSlash(): self;

    /**
     * Returns an instance without a trailing slash.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component without a trailing slash
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in a object in invalid state.
     */
    public function withoutTrailingSlash(): self;
}
