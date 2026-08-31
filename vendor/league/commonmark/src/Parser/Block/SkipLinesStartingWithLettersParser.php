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

namespace League\CommonMark\Parser\Block;

use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\CommonMark\Util\RegexHelper;

/**
 * @internal
 *
 * This "parser" is actually a performance optimization.
 *
 * Most lines in a typical Markdown document probably won't match a block start. This is especially true for lines starting
 * with letters - nothing in the core CommonMark spec or our supported extensions will match those lines as blocks. Therefore,
 * if we can identify those lines and skip block start parsing, we can optimize performance by ~10%.
 *
 * Previously this optimization was hard-coded in the MarkdownParser but did not allow users to override this behavior.
 * By implementing this optimization as a block parser instead, users wanting custom blocks starting with letters
 * can instead register their block parser with a higher priority to ensure their parser is always called first.
 */
final class SkipLinesStartingWithLettersParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if (! $cursor->isIndented() && RegexHelper::isLetter($cursor->getNextNonSpaceCharacter())) {
            $cursor->advanceToNextNonSpaceOrTab();

            return BlockStart::abort();
        }

        return BlockStart::none();
    }
}
