<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Reference;

use League\CommonMark\Normalizer\TextNormalizer;

/**
 * A collection of references, indexed by label
 */
final class ReferenceMap implements ReferenceMapInterface
{
    /** @psalm-readonly */
    private TextNormalizer $normalizer;

    /**
     * @var array<string, ReferenceInterface>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $references = [];

    public function __construct()
    {
        $this->normalizer = new TextNormalizer();
    }

    public function add(ReferenceInterface $reference): void
    {
        // Normalize the key
        $key = $this->normalizer->normalize($reference->getLabel());
        // Store the reference
        $this->references[$key] = $reference;
    }

    public function contains(string $label): bool
    {
        if ($this->references === []) {
            return false;
        }

        $label = $this->normalizer->normalize($label);

        return isset($this->references[$label]);
    }

    public function get(string $label): ?ReferenceInterface
    {
        if ($this->references === []) {
            return null;
        }

        $label = $this->normalizer->normalize($label);

        return $this->references[$label] ?? null;
    }

    /**
     * @return \Traversable<string, ReferenceInterface>
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->references as $normalizedLabel => $reference) {
            yield $normalizedLabel => $reference;
        }
    }

    public function count(): int
    {
        return \count($this->references);
    }
}
