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

namespace League\CommonMark\Extension\DescriptionList\Parser;

use League\CommonMark\Extension\DescriptionList\Node\Description;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

final class DescriptionStartParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented()) {
            return BlockStart::none();
        }

        $cursor->advanceToNextNonSpaceOrTab();
        if ($cursor->match('/^:[ \t]+/') === null) {
            return BlockStart::none();
        }

        $terms = $parserState->getParagraphContent();

        $activeBlock = $parserState->getActiveBlockParser()->getBlock();

        if ($terms !== null && $terms !== '') {
            // New description; tight; term(s) sitting in pending block that we will replace
            return BlockStart::of(...[new DescriptionListContinueParser()], ...self::splitTerms($terms), ...[new DescriptionContinueParser(true, $cursor->getPosition())])
                ->at($cursor)
                ->replaceActiveBlockParser();
        }

        if ($activeBlock instanceof Paragraph && $activeBlock->parent() instanceof Description) {
            // Additional description in the same list as the parent description
            return BlockStart::of(new DescriptionContinueParser(true, $cursor->getPosition()))->at($cursor);
        }

        if ($activeBlock->lastChild() instanceof Paragraph) {
            // New description; loose; term(s) sitting in previous closed paragraph block
            return BlockStart::of(new DescriptionContinueParser(false, $cursor->getPosition()))->at($cursor);
        }

        // No preceding terms
        return BlockStart::none();
    }

    /**
     * @return array<int, DescriptionTermContinueParser>
     */
    private static function splitTerms(string $terms): array
    {
        $ret = [];
        foreach (\explode("\n", $terms) as $term) {
            $ret[] = new DescriptionTermContinueParser($term);
        }

        return $ret;
    }
}
