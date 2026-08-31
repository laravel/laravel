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

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Util\UrlEncoder;

final class AutolinkParser implements InlineParserInterface
{
    private const EMAIL_REGEX      = '<([a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*)>';
    private const OTHER_LINK_REGEX = '<([A-Za-z][A-Za-z0-9.+-]{1,31}:[^<>\x00-\x20]*)>';

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex(self::EMAIL_REGEX . '|' . self::OTHER_LINK_REGEX);
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());
        $matches = $inlineContext->getMatches();

        if ($matches[1] !== '') {
            $inlineContext->getContainer()->appendChild(new Link('mailto:' . UrlEncoder::unescapeAndEncode($matches[1]), $matches[1]));

            return true;
        }

        if ($matches[2] !== '') {
            $inlineContext->getContainer()->appendChild(new Link(UrlEncoder::unescapeAndEncode($matches[2]), $matches[2]));

            return true;
        }

        return false; // This should never happen
    }
}
