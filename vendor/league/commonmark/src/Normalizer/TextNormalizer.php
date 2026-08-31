<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Normalizer;

/***
 * Normalize text input using the steps given by the CommonMark spec to normalize labels
 *
 * @see https://spec.commonmark.org/0.29/#matches
 *
 * @psalm-immutable
 */
final class TextNormalizer implements TextNormalizerInterface
{
    /**
     * {@inheritDoc}
     *
     * @psalm-pure
     */
    public function normalize(string $text, array $context = []): string
    {
        // Collapse internal whitespace to single space and remove
        // leading/trailing whitespace
        $text = \preg_replace('/[ \t\r\n]+/', ' ', \trim($text));
        \assert(\is_string($text));

        // Is it strictly ASCII? If so, we can use strtolower() instead (faster)
        if (\mb_check_encoding($text, 'ASCII')) {
            return \strtolower($text);
        }

        return \mb_convert_case($text, \MB_CASE_FOLD, 'UTF-8');
    }
}
