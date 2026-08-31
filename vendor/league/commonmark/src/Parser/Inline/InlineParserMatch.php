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

namespace League\CommonMark\Parser\Inline;

use League\CommonMark\Exception\InvalidArgumentException;

final class InlineParserMatch
{
    private string $regex;

    private bool $caseSensitive;

    private function __construct(string $regex, bool $caseSensitive = false)
    {
        $this->regex         = $regex;
        $this->caseSensitive = $caseSensitive;
    }

    public function caseSensitive(): self
    {
        $this->caseSensitive = true;

        return $this;
    }

    /**
     * @internal
     *
     * @psalm-return non-empty-string
     */
    public function getRegex(): string
    {
        return '/' . $this->regex . '/' . ($this->caseSensitive ? '' : 'i');
    }

    /**
     * Match the given string (case-insensitive)
     */
    public static function string(string $str): self
    {
        return new self(\preg_quote($str, '/'));
    }

    /**
     * Match any of the given strings (case-insensitive)
     */
    public static function oneOf(string ...$str): self
    {
        return new self(\implode('|', \array_map(static fn (string $str): string => \preg_quote($str, '/'), $str)));
    }

    /**
     * Match a partial regular expression without starting/ending delimiters, anchors, or flags
     */
    public static function regex(string $regex): self
    {
        return new self($regex);
    }

    public static function join(self ...$definitions): self
    {
        $regex         = '';
        $caseSensitive = null;
        foreach ($definitions as $definition) {
            $regex .= '(' . $definition->regex . ')';

            if ($caseSensitive === null) {
                $caseSensitive = $definition->caseSensitive;
            } elseif ($caseSensitive !== $definition->caseSensitive) {
                throw new InvalidArgumentException('Case-sensitive and case-insensitive definitions cannot be combined');
            }
        }

        return new self($regex, $caseSensitive ?? false);
    }
}
