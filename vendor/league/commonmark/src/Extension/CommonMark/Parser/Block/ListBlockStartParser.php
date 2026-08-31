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

namespace League\CommonMark\Extension\CommonMark\Parser\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\CommonMark\Util\RegexHelper;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class ListBlockStartParser implements BlockStartParserInterface, ConfigurationAwareInterface
{
    /** @psalm-readonly-allow-private-mutation */
    private ?ConfigurationInterface $config = null;

    /**
     * @psalm-var non-empty-string|null
     *
     * @psalm-readonly-allow-private-mutation
     */
    private ?string $listMarkerRegex = null;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented()) {
            return BlockStart::none();
        }

        $listData = $this->parseList($cursor, $parserState->getParagraphContent() !== null);
        if ($listData === null) {
            return BlockStart::none();
        }

        $listItemParser = new ListItemParser($listData);

        // prepend the list block if needed
        $matched = $parserState->getLastMatchedBlockParser();
        if (! ($matched instanceof ListBlockParser) || ! $listData->equals($matched->getBlock()->getListData())) {
            $listBlockParser = new ListBlockParser($listData);
            // We start out with assuming a list is tight. If we find a blank line, we set it to loose later.
            // TODO for 3.0: Just make them tight by default in the block so we can remove this call
            $listBlockParser->getBlock()->setTight(true);

            return BlockStart::of($listBlockParser, $listItemParser)->at($cursor);
        }

        return BlockStart::of($listItemParser)->at($cursor);
    }

    private function parseList(Cursor $cursor, bool $inParagraph): ?ListData
    {
        $indent = $cursor->getIndent();

        $tmpCursor = clone $cursor;
        $tmpCursor->advanceToNextNonSpaceOrTab();
        $rest = $tmpCursor->getRemainder();

        if (\preg_match($this->listMarkerRegex ?? $this->generateListMarkerRegex(), $rest) === 1) {
            $data               = new ListData();
            $data->markerOffset = $indent;
            $data->type         = ListBlock::TYPE_BULLET;
            $data->delimiter    = null;
            $data->bulletChar   = $rest[0];
            $markerLength       = 1;
        } elseif (($matches = RegexHelper::matchFirst('/^(\d{1,9})([.)])/', $rest)) && (! $inParagraph || $matches[1] === '1')) {
            $data               = new ListData();
            $data->markerOffset = $indent;
            $data->type         = ListBlock::TYPE_ORDERED;
            $data->start        = (int) $matches[1];
            $data->delimiter    = $matches[2] === '.' ? ListBlock::DELIM_PERIOD : ListBlock::DELIM_PAREN;
            $data->bulletChar   = null;
            $markerLength       = \strlen($matches[0]);
        } else {
            return null;
        }

        // Make sure we have spaces after
        $nextChar = $tmpCursor->peek($markerLength);
        if (! ($nextChar === null || $nextChar === "\t" || $nextChar === ' ')) {
            return null;
        }

        // If it interrupts paragraph, make sure first line isn't blank
        if ($inParagraph && ! RegexHelper::matchAt(RegexHelper::REGEX_NON_SPACE, $rest, $markerLength)) {
            return null;
        }

        $cursor->advanceToNextNonSpaceOrTab(); // to start of marker
        $cursor->advanceBy($markerLength, true); // to end of marker
        $data->padding = self::calculateListMarkerPadding($cursor, $markerLength);

        return $data;
    }

    private static function calculateListMarkerPadding(Cursor $cursor, int $markerLength): int
    {
        $start          = $cursor->saveState();
        $spacesStartCol = $cursor->getColumn();

        while ($cursor->getColumn() - $spacesStartCol < 5) {
            if (! $cursor->advanceBySpaceOrTab()) {
                break;
            }
        }

        $blankItem         = $cursor->peek() === null;
        $spacesAfterMarker = $cursor->getColumn() - $spacesStartCol;

        if ($spacesAfterMarker >= 5 || $spacesAfterMarker < 1 || $blankItem) {
            $cursor->restoreState($start);
            $cursor->advanceBySpaceOrTab();

            return $markerLength + 1;
        }

        return $markerLength + $spacesAfterMarker;
    }

    /**
     * @psalm-return non-empty-string
     */
    private function generateListMarkerRegex(): string
    {
        // No configuration given - use the defaults
        if ($this->config === null) {
            return $this->listMarkerRegex = '/^[*+-]/';
        }

        $markers = $this->config->get('commonmark/unordered_list_markers');
        \assert(\is_array($markers));

        return $this->listMarkerRegex = '/^[' . \preg_quote(\implode('', $markers), '/') . ']/';
    }
}
