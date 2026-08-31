<?php

declare(strict_types=1);

namespace League\Flysystem;

use ArrayIterator;
use Generator;
use IteratorAggregate;
use Traversable;

/**
 * @template T
 */
class DirectoryListing implements IteratorAggregate
{
    /**
     * @param iterable<T> $listing
     */
    public function __construct(private iterable $listing)
    {
    }

    /**
     * @param callable(T): bool $filter
     *
     * @return DirectoryListing<T>
     */
    public function filter(callable $filter): DirectoryListing
    {
        $generator = (static function (iterable $listing) use ($filter): Generator {
            foreach ($listing as $item) {
                if ($filter($item)) {
                    yield $item;
                }
            }
        })($this->listing);

        return new DirectoryListing($generator);
    }

    /**
     * @template R
     *
     * @param callable(T): R $mapper
     *
     * @return DirectoryListing<R>
     */
    public function map(callable $mapper): DirectoryListing
    {
        $generator = (static function (iterable $listing) use ($mapper): Generator {
            foreach ($listing as $item) {
                yield $mapper($item);
            }
        })($this->listing);

        return new DirectoryListing($generator);
    }

    /**
     * @return DirectoryListing<T>
     */
    public function sortByPath(): DirectoryListing
    {
        $listing = $this->toArray();

        usort($listing, function (StorageAttributes $a, StorageAttributes $b) {
            return $a->path() <=> $b->path();
        });

        return new DirectoryListing($listing);
    }

    /**
     * @return Traversable<T>
     */
    public function getIterator(): Traversable
    {
        return $this->listing instanceof Traversable
            ? $this->listing
            : new ArrayIterator($this->listing);
    }

    /**
     * @return T[]
     */
    public function toArray(): array
    {
        return $this->listing instanceof Traversable
            ? iterator_to_array($this->listing, false)
            : (array) $this->listing;
    }
}
