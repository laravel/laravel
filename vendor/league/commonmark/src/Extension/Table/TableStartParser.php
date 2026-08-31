<?php

declare(strict_types=1);

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Table;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Block\ParagraphParser;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

final class TableStartParser implements BlockStartParserInterface
{
    private int $maxAutocompletedCells;

    public function __construct(int $maxAutocompletedCells = TableParser::DEFAULT_MAX_AUTOCOMPLETED_CELLS)
    {
        $this->maxAutocompletedCells = $maxAutocompletedCells;
    }

    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        $paragraph = $parserState->getParagraphContent();
        if ($paragraph === null || \strpos($paragraph, '|') === false) {
            return BlockStart::none();
        }

        $columns = self::parseSeparator($cursor);
        if (\count($columns) === 0) {
            return BlockStart::none();
        }

        $lastLineBreak = \strrpos($paragraph, "\n");
        $lastLine      = $lastLineBreak === false ? $paragraph : \substr($paragraph, $lastLineBreak + 1);

        $headerCells = TableParser::split($lastLine);
        if (\count($headerCells) > \count($columns)) {
            return BlockStart::none();
        }

        $cursor->advanceToEnd();

        $parsers = [];

        if ($lastLineBreak !== false) {
            $p = new ParagraphParser();
            $p->addLine(\substr($paragraph, 0, $lastLineBreak));
            $parsers[] = $p;
        }

        $parsers[] = new TableParser($columns, $headerCells, $this->maxAutocompletedCells);

        return BlockStart::of(...$parsers)
            ->at($cursor)
            ->replaceActiveBlockParser();
    }

    /**
     * @return array<int, string|null>
     *
     * @psalm-return array<int, TableCell::ALIGN_*|null>
     *
     * @phpstan-return array<int, TableCell::ALIGN_*|null>
     */
    private static function parseSeparator(Cursor $cursor): array
    {
        $columns = [];
        $pipes   = 0;
        $valid   = false;

        while (! $cursor->isAtEnd()) {
            switch ($c = $cursor->getCurrentCharacter()) {
                case '|':
                    $cursor->advanceBy(1);
                    $pipes++;
                    if ($pipes > 1) {
                        // More than one adjacent pipe not allowed
                        return [];
                    }

                    // Need at least one pipe, even for a one-column table
                    $valid = true;
                    break;
                case '-':
                case ':':
                    if ($pipes === 0 && \count($columns) > 0) {
                        // Need a pipe after the first column (first column doesn't need to start with one)
                        return [];
                    }

                    $left  = false;
                    $right = false;
                    if ($c === ':') {
                        $left = true;
                        $cursor->advanceBy(1);
                    }

                    if ($cursor->match('/^-+/') === null) {
                        // Need at least one dash
                        return [];
                    }

                    if ($cursor->getCurrentCharacter() === ':') {
                        $right = true;
                        $cursor->advanceBy(1);
                    }

                    $columns[] = self::getAlignment($left, $right);
                    // Next, need another pipe
                    $pipes = 0;
                    break;
                case ' ':
                case "\t":
                    // White space is allowed between pipes and columns
                    $cursor->advanceToNextNonSpaceOrTab();
                    break;
                default:
                    // Any other character is invalid
                    return [];
            }
        }

        if (! $valid) {
            return [];
        }

        return $columns;
    }

    /**
     * @psalm-return TableCell::ALIGN_*|null
     *
     * @phpstan-return TableCell::ALIGN_*|null
     *
     * @psalm-pure
     */
    private static function getAlignment(bool $left, bool $right): ?string
    {
        if ($left && $right) {
            return TableCell::ALIGN_CENTER;
        }

        if ($left) {
            return TableCell::ALIGN_LEFT;
        }

        if ($right) {
            return TableCell::ALIGN_RIGHT;
        }

        return null;
    }
}
