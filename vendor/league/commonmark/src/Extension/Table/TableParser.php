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

use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\BlockContinueParserWithInlinesInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\InlineParserEngineInterface;
use League\CommonMark\Util\ArrayCollection;

final class TableParser extends AbstractBlockContinueParser implements BlockContinueParserWithInlinesInterface
{
    /**
     * @internal
     */
    public const DEFAULT_MAX_AUTOCOMPLETED_CELLS = 10_000;

    /** @psalm-readonly */
    private Table $block;

    /**
     * @var ArrayCollection<string>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private ArrayCollection $bodyLines;

    /**
     * @var array<int, string|null>
     * @psalm-var array<int, TableCell::ALIGN_*|null>
     * @phpstan-var array<int, TableCell::ALIGN_*|null>
     *
     * @psalm-readonly
     */
    private array $columns;

    /**
     * @var array<int, string>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $headerCells;

    /** @psalm-readonly-allow-private-mutation */
    private bool $nextIsSeparatorLine = true;

    private int $remainingAutocompletedCells;

    /**
     * @param array<int, string|null> $columns
     * @param array<int, string>      $headerCells
     *
     * @psalm-param array<int, TableCell::ALIGN_*|null> $columns
     *
     * @phpstan-param array<int, TableCell::ALIGN_*|null> $columns
     */
    public function __construct(array $columns, array $headerCells, int $remainingAutocompletedCells = self::DEFAULT_MAX_AUTOCOMPLETED_CELLS)
    {
        $this->block                       = new Table();
        $this->bodyLines                   = new ArrayCollection();
        $this->columns                     = $columns;
        $this->headerCells                 = $headerCells;
        $this->remainingAutocompletedCells = $remainingAutocompletedCells;
    }

    public function canHaveLazyContinuationLines(): bool
    {
        return true;
    }

    public function getBlock(): Table
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if (\strpos($cursor->getLine(), '|') === false) {
            return BlockContinue::none();
        }

        return BlockContinue::at($cursor);
    }

    public function addLine(string $line): void
    {
        if ($this->nextIsSeparatorLine) {
            $this->nextIsSeparatorLine = false;
        } else {
            $this->bodyLines[] = $line;
        }
    }

    public function parseInlines(InlineParserEngineInterface $inlineParser): void
    {
        $headerColumns = \count($this->headerCells);

        $head = new TableSection(TableSection::TYPE_HEAD);
        $this->block->appendChild($head);

        $headerRow = new TableRow();
        $head->appendChild($headerRow);
        for ($i = 0; $i < $headerColumns; $i++) {
            $cell      = $this->headerCells[$i];
            $tableCell = $this->parseCell($cell, $i, $inlineParser);
            $tableCell->setType(TableCell::TYPE_HEADER);
            $headerRow->appendChild($tableCell);
        }

        $body = null;
        foreach ($this->bodyLines as $rowLine) {
            $cells = self::split($rowLine);
            $row   = new TableRow();

            // Body can not have more columns than head
            for ($i = 0; $i < $headerColumns; $i++) {
                // It can have less columns though, in which case we'll autocomplete the empty ones (up to some limit)
                if (! isset($cells[$i]) && $this->remainingAutocompletedCells-- <= 0) {
                    // Too many cells were auto-completed, so we'll just stop here
                    return;
                }

                $cell      = $cells[$i] ?? '';
                $tableCell = $this->parseCell($cell, $i, $inlineParser);
                $row->appendChild($tableCell);
            }

            if ($body === null) {
                // It's valid to have a table without body. In that case, don't add an empty TableBody node.
                $body = new TableSection();
                $this->block->appendChild($body);
            }

            $body->appendChild($row);
        }
    }

    private function parseCell(string $cell, int $column, InlineParserEngineInterface $inlineParser): TableCell
    {
        $tableCell = new TableCell(TableCell::TYPE_DATA, $this->columns[$column] ?? null);

        if ($cell !== '') {
            $inlineParser->parse(\trim($cell), $tableCell);
        }

        return $tableCell;
    }

    /**
     * @internal
     *
     * @return array<int, string>
     */
    public static function split(string $line): array
    {
        $cursor = new Cursor(\trim($line));

        if ($cursor->getCurrentCharacter() === '|') {
            $cursor->advanceBy(1);
        }

        $cells = [];
        $sb    = '';

        while (! $cursor->isAtEnd()) {
            switch ($c = $cursor->getCurrentCharacter()) {
                case '\\':
                    if ($cursor->peek() === '|') {
                        // Pipe is special for table parsing. An escaped pipe doesn't result in a new cell, but is
                        // passed down to inline parsing as an unescaped pipe. Note that that applies even for the `\|`
                        // in an input like `\\|` - in other words, table parsing doesn't support escaping backslashes.
                        $sb .= '|';
                        $cursor->advanceBy(1);
                    } else {
                        // Preserve backslash before other characters or at end of line.
                        $sb .= '\\';
                    }

                    break;
                case '|':
                    $cells[] = $sb;
                    $sb      = '';
                    break;
                default:
                    $sb .= $c;
            }

            $cursor->advanceBy(1);
        }

        if ($sb !== '') {
            $cells[] = $sb;
        }

        return $cells;
    }
}
