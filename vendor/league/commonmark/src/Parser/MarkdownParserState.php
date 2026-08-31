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

namespace League\CommonMark\Parser;

use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\ParagraphParser;

/**
 * @internal You should rely on the interface instead
 */
final class MarkdownParserState implements MarkdownParserStateInterface
{
    /** @psalm-readonly */
    private BlockContinueParserInterface $activeBlockParser;

    /** @psalm-readonly */
    private BlockContinueParserInterface $lastMatchedBlockParser;

    public function __construct(BlockContinueParserInterface $activeBlockParser, BlockContinueParserInterface $lastMatchedBlockParser)
    {
        $this->activeBlockParser      = $activeBlockParser;
        $this->lastMatchedBlockParser = $lastMatchedBlockParser;
    }

    public function getActiveBlockParser(): BlockContinueParserInterface
    {
        return $this->activeBlockParser;
    }

    public function getLastMatchedBlockParser(): BlockContinueParserInterface
    {
        return $this->lastMatchedBlockParser;
    }

    public function getParagraphContent(): ?string
    {
        if (! $this->lastMatchedBlockParser instanceof ParagraphParser) {
            return null;
        }

        $paragraphParser = $this->lastMatchedBlockParser;
        $content         = $paragraphParser->getContentString();

        return $content === '' ? null : $content;
    }
}
