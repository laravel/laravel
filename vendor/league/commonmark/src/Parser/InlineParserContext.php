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

namespace League\CommonMark\Parser;

use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Reference\ReferenceMapInterface;

final class InlineParserContext
{
    /** @psalm-readonly */
    private AbstractBlock $container;

    /** @psalm-readonly */
    private ReferenceMapInterface $referenceMap;

    /** @psalm-readonly */
    private Cursor $cursor;

    /** @psalm-readonly */
    private DelimiterStack $delimiterStack;

    /**
     * @var string[]
     * @psalm-var non-empty-array<string>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $matches;

    public function __construct(Cursor $contents, AbstractBlock $container, ReferenceMapInterface $referenceMap, int $maxDelimitersPerLine = PHP_INT_MAX)
    {
        $this->referenceMap   = $referenceMap;
        $this->container      = $container;
        $this->cursor         = $contents;
        $this->delimiterStack = new DelimiterStack($maxDelimitersPerLine);
    }

    public function getContainer(): AbstractBlock
    {
        return $this->container;
    }

    public function getReferenceMap(): ReferenceMapInterface
    {
        return $this->referenceMap;
    }

    public function getCursor(): Cursor
    {
        return $this->cursor;
    }

    public function getDelimiterStack(): DelimiterStack
    {
        return $this->delimiterStack;
    }

    /**
     * @return string The full text that matched the InlineParserMatch definition
     */
    public function getFullMatch(): string
    {
        return $this->matches[0];
    }

    /**
     * @return int The length of the full match (in characters, not bytes)
     */
    public function getFullMatchLength(): int
    {
        return \mb_strlen($this->matches[0], 'UTF-8');
    }

    /**
     * @return string[] Similar to preg_match(), index 0 will contain the full match, and any other array elements will be captured sub-matches
     *
     * @psalm-return non-empty-array<string>
     */
    public function getMatches(): array
    {
        return $this->matches;
    }

    /**
     * @return string[]
     */
    public function getSubMatches(): array
    {
        return \array_slice($this->matches, 1);
    }

    /**
     * @param string[] $matches
     *
     * @psalm-param non-empty-array<string> $matches
     */
    public function withMatches(array $matches): InlineParserContext
    {
        $ctx = clone $this;

        $ctx->matches = $matches;

        return $ctx;
    }
}
