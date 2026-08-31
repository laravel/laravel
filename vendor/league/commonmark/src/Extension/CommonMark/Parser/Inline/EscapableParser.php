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

namespace League\CommonMark\Extension\CommonMark\Parser\Inline;

use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Util\RegexHelper;

final class EscapableParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::string('\\');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor   = $inlineContext->getCursor();
        $nextChar = $cursor->peek();

        if ($nextChar === "\n") {
            $cursor->advanceBy(2);
            $inlineContext->getContainer()->appendChild(new Newline(Newline::HARDBREAK));

            return true;
        }

        if ($nextChar !== null && RegexHelper::isEscapable($nextChar)) {
            $cursor->advanceBy(2);
            $inlineContext->getContainer()->appendChild(new Text($nextChar));

            return true;
        }

        $cursor->advanceBy(1);
        $inlineContext->getContainer()->appendChild(new Text('\\'));

        return true;
    }
}
