<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

// Help opcache.preload discover always-needed symbols
class_exists(AcceptHeaderItem::class);

/**
 * Represents an Accept-* header.
 *
 * An accept header is compound with a list of items,
 * sorted by descending quality.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class AcceptHeader
{
    /**
     * @var array<string, AcceptHeaderItem>
     */
    private array $items = [];

    private bool $sorted = true;

    /**
     * @param AcceptHeaderItem[] $items
     */
    public function __construct(array $items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Builds an AcceptHeader instance from a string.
     */
    public static function fromString(?string $headerValue): self
    {
        $items = [];
        foreach (HeaderUtils::split($headerValue ?? '', ',;=') as $i => $parts) {
            $part = array_shift($parts);
            $item = new AcceptHeaderItem($part[0], HeaderUtils::combine($parts));

            $items[] = $item->setIndex($i);
        }

        return new self($items);
    }

    /**
     * Returns header value's string representation.
     */
    public function __toString(): string
    {
        return implode(',', $this->items);
    }

    /**
     * Tests if header has given value.
     */
    public function has(string $value): bool
    {
        $canonicalKey = $this->getCanonicalKey(AcceptHeaderItem::fromString($value));

        return isset($this->items[$canonicalKey]);
    }

    /**
     * Returns given value's item, if exists.
     */
    public function get(string $value): ?AcceptHeaderItem
    {
        $queryItem = AcceptHeaderItem::fromString($value.';q=1');
        $canonicalKey = $this->getCanonicalKey($queryItem);

        if (isset($this->items[$canonicalKey])) {
            return $this->items[$canonicalKey];
        }

        // Collect and filter matching candidates
        if (!$candidates = array_filter($this->items, fn (AcceptHeaderItem $item) => $this->matches($item, $queryItem))) {
            return null;
        }

        usort(
            $candidates,
            fn ($a, $b) => $this->getSpecificity($b, $queryItem) <=> $this->getSpecificity($a, $queryItem) // Descending specificity
                ?: $b->getQuality() <=> $a->getQuality() // Descending quality
                ?: $a->getIndex() <=> $b->getIndex() // Ascending index (stability)
        );

        return reset($candidates);
    }

    /**
     * Adds an item.
     *
     * @return $this
     */
    public function add(AcceptHeaderItem $item): static
    {
        $this->items[$this->getCanonicalKey($item)] = $item;
        $this->sorted = false;

        return $this;
    }

    /**
     * Returns all items.
     *
     * @return AcceptHeaderItem[]
     */
    public function all(): array
    {
        $this->sort();

        return $this->items;
    }

    /**
     * Filters items on their value using given regex.
     */
    public function filter(string $pattern): self
    {
        return new self(array_filter($this->items, static fn ($item) => preg_match($pattern, $item->getValue())));
    }

    /**
     * Returns first item.
     */
    public function first(): ?AcceptHeaderItem
    {
        $this->sort();

        return $this->items ? reset($this->items) : null;
    }

    /**
     * Sorts items by descending quality.
     */
    private function sort(): void
    {
        if (!$this->sorted) {
            uasort($this->items, static fn ($a, $b) => $b->getQuality() <=> $a->getQuality() ?: $a->getIndex() <=> $b->getIndex());

            $this->sorted = true;
        }
    }

    /**
     * Generates the canonical key for storing/retrieving an item.
     */
    private function getCanonicalKey(AcceptHeaderItem $item): string
    {
        $parts = [];

        // Normalize and sort attributes for consistent key generation
        $attributes = $this->getMediaParams($item);
        ksort($attributes);

        foreach ($attributes as $name => $value) {
            if (null === $value) {
                $parts[] = $name; // Flag parameter (e.g., "flowed")
                continue;
            }

            // Quote values containing spaces, commas, semicolons, or equals per RFC 9110
            // This handles cases like 'format="value with space"' or similar.
            $quotedValue = \is_string($value) && preg_match('/[\s;,=]/', $value) ? '"'.addcslashes($value, '"\\').'"' : $value;

            $parts[] = $name.'='.$quotedValue;
        }

        return $item->getValue().($parts ? ';'.implode(';', $parts) : '');
    }

    /**
     * Checks if a given header item (range) matches a queried item (value).
     *
     * @param AcceptHeaderItem $rangeItem The item from the Accept header (e.g., text/*;format=flowed)
     * @param AcceptHeaderItem $queryItem The item being queried (e.g., text/plain;format=flowed;charset=utf-8)
     */
    private function matches(AcceptHeaderItem $rangeItem, AcceptHeaderItem $queryItem): bool
    {
        $rangeValue = strtolower($rangeItem->getValue());
        $queryValue = strtolower($queryItem->getValue());

        // Handle universal wildcard ranges
        if ('*' === $rangeValue || '*/*' === $rangeValue) {
            return $this->rangeParametersMatch($rangeItem, $queryItem);
        }

        // Queries for '*' only match wildcard ranges (handled above)
        if ('*' === $queryValue) {
            return false;
        }

        // Ensure media vs. non-media consistency
        $isQueryMedia = str_contains($queryValue, '/');
        $isRangeMedia = str_contains($rangeValue, '/');

        if ($isQueryMedia !== $isRangeMedia) {
            return false;
        }

        // Non-media: exact match only (wildcards handled above)
        if (!$isQueryMedia) {
            return $rangeValue === $queryValue && $this->rangeParametersMatch($rangeItem, $queryItem);
        }

        // Media type: type/subtype with wildcards
        [$queryType, $querySubtype] = explode('/', $queryValue, 2);
        [$rangeType, $rangeSubtype] = explode('/', $rangeValue, 2) + [1 => '*'];

        if ('*' !== $rangeType && $rangeType !== $queryType) {
            return false;
        }

        if ('*' !== $rangeSubtype && $rangeSubtype !== $querySubtype) {
            return false;
        }

        // Parameters must match
        return $this->rangeParametersMatch($rangeItem, $queryItem);
    }

    /**
     * Checks if the parameters of a range item are satisfied by the query item.
     *
     * Parameters are case-insensitive; range params must be a subset of query params.
     */
    private function rangeParametersMatch(AcceptHeaderItem $rangeItem, AcceptHeaderItem $queryItem): bool
    {
        $queryAttributes = $this->getMediaParams($queryItem);
        $rangeAttributes = $this->getMediaParams($rangeItem);

        foreach ($rangeAttributes as $name => $rangeValue) {
            if (!\array_key_exists($name, $queryAttributes)) {
                return false; // Missing required param
            }

            $queryValue = $queryAttributes[$name];

            if (null === $rangeValue) {
                return null === $queryValue; // Both flags or neither
            }

            if (null === $queryValue || strtolower($queryValue) !== strtolower($rangeValue)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculates a specificity score for sorting: media precision + param count.
     */
    private function getSpecificity(AcceptHeaderItem $item, AcceptHeaderItem $queryItem): int
    {
        $rangeValue = strtolower($item->getValue());
        $queryValue = strtolower($queryItem->getValue());

        $paramCount = \count($this->getMediaParams($item));

        $isQueryMedia = str_contains($queryValue, '/');
        $isRangeMedia = str_contains($rangeValue, '/');

        if (!$isQueryMedia && !$isRangeMedia) {
            return ('*' !== $rangeValue ? 2000 : 1000) + $paramCount;
        }

        [$rangeType, $rangeSubtype] = explode('/', $rangeValue, 2) + [1 => '*'];

        $specificity = match (true) {
            '*' !== $rangeSubtype => 3000, // Exact subtype (text/plain)
            '*' !== $rangeType => 2000,    // Type wildcard (text/*)
            default => 1000,               // Full wildcard (*/* or *)
        };

        return $specificity + $paramCount;
    }

    /**
     * Returns normalized attributes: keys lowercased, excluding 'q'.
     */
    private function getMediaParams(AcceptHeaderItem $item): array
    {
        $attributes = array_change_key_case($item->getAttributes(), \CASE_LOWER);
        unset($attributes['q']);

        return $attributes;
    }
}
