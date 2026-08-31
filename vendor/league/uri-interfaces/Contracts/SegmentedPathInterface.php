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

use Countable;
use Iterator;
use IteratorAggregate;
use League\Uri\Exceptions\SyntaxError;
use Stringable;

/**
 * @extends IteratorAggregate<string>
 */
interface SegmentedPathInterface extends Countable, IteratorAggregate, PathInterface
{
    /**
     * Returns the total number of segments in the path.
     */
    public function count(): int;

    /**
     * Iterate over the path segment.
     *
     * @return Iterator<string>
     */
    public function getIterator(): Iterator;

    /**
     * Returns parent directory's path.
     */
    public function getDirname(): string;

    /**
     * Returns the path basename.
     */
    public function getBasename(): string;

    /**
     * Returns the basename extension.
     */
    public function getExtension(): string;

    /**
     * Retrieves a single path segment.
     *
     * If the segment offset has not been set, returns null.
     */
    public function get(int $offset): ?string;

    /**
     * Returns the associated key for a specific segment.
     *
     * If a value is specified only the keys associated with
     * the given value will be returned
     *
     * @return array<int>
     */
    public function keys(Stringable|string|null $segment = null): array;

    /**
     * Appends a segment to the path.
     */
    public function append(Stringable|string $path): self;

    /**
     * Extracts a slice of $length elements starting at position $offset from the host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the selected slice.
     *
     * If $length is null it returns all elements from $offset to the end of the Path.
     */
    public function slice(int $offset, ?int $length = null): self;

    /**
     * Prepends a segment to the path.
     */
    public function prepend(Stringable|string $path): self;

    /**
     * Returns an instance with the modified segment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the new segment
     *
     * If $key is non-negative, the added segment will be the segment at $key position from the start.
     * If $key is negative, the added segment will be the segment at $key position from the end.
     *
     * @throws SyntaxError If the key is invalid
     */
    public function withSegment(int $key, Stringable|string $segment): self;

    /**
     * Returns an instance without the specified segment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified component
     *
     * If $key is non-negative, the removed segment will be the segment at $key position from the start.
     * If $key is negative, the removed segment will be the segment at $key position from the end.
     *
     * @throws SyntaxError If the key is invalid
     */
    public function withoutSegment(int ...$keys): self;

    /**
     * Returns an instance without duplicate delimiters.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component normalized by removing
     * multiple consecutive empty segment
     */
    public function withoutEmptySegments(): self;

    /**
     * Returns an instance with the specified parent directory's path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the extension basename modified.
     */
    public function withDirname(Stringable|string $path): self;

    /**
     * Returns an instance with the specified basename.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the extension basename modified.
     */
    public function withBasename(Stringable|string $basename): self;

    /**
     * Returns an instance with the specified basename extension.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the extension basename modified.
     */
    public function withExtension(Stringable|string $extension): self;
}
