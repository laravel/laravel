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

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\CommonMark\Util\RegexHelper;

class HeadingStartParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented() || ! \in_array($cursor->getNextNonSpaceCharacter(), ['#', '-', '='], true)) {
            return BlockStart::none();
        }

        $cursor->advanceToNextNonSpaceOrTab();

        if ($atxHeading = self::getAtxHeader($cursor)) {
            return BlockStart::of($atxHeading)->at($cursor);
        }

        $setextHeadingLevel = self::getSetextHeadingLevel($cursor);
        if ($setextHeadingLevel > 0) {
            $content = $parserState->getParagraphContent();
            if ($content !== null) {
                $cursor->advanceToEnd();

                return BlockStart::of(new HeadingParser($setextHeadingLevel, $content))
                    ->at($cursor)
                    ->replaceActiveBlockParser();
            }
        }

        return BlockStart::none();
    }

    private static function getAtxHeader(Cursor $cursor): ?HeadingParser
    {
        $match = RegexHelper::matchFirst('/^#{1,6}(?:[ \t]+|$)/', $cursor->getRemainder());
        if (! $match) {
            return null;
        }

        $cursor->advanceToNextNonSpaceOrTab();
        $cursor->advanceBy(\strlen($match[0]));

        $level = \strlen(\trim($match[0]));
        $str   = $cursor->getRemainder();
        $str   = \preg_replace('/^[ \t]*#+[ \t]*$/', '', $str);
        \assert(\is_string($str));
        $str = \preg_replace('/[ \t]+#+[ \t]*$/', '', $str);
        \assert(\is_string($str));

        return new HeadingParser($level, $str);
    }

    private static function getSetextHeadingLevel(Cursor $cursor): int
    {
        $match = RegexHelper::matchFirst('/^(?:=+|-+)[ \t]*$/', $cursor->getRemainder());
        if ($match === null) {
            return 0;
        }

        return $match[0][0] === '=' ? 1 : 2;
    }
}
