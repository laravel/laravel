<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Normalizer;

/**
 * Creates a normalized version of the given input text
 */
interface TextNormalizerInterface
{
    /**
     * @param string               $text    The text to normalize
     * @param array<string, mixed> $context Additional context about the text being normalized (optional)
     *
     * $context may include (but is not required to include) the following:
     *   - `prefix` - A string prefix to prepend to each normalized result
     *   - `length` - The requested maximum length
     *   - `node` - The node we're normalizing text for
     *
     * Implementations do not have to use or respect any information within that $context
     */
    public function normalize(string $text, array $context = []): string;
}
