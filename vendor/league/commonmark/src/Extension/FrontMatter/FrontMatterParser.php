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

namespace League\CommonMark\Extension\FrontMatter;

use League\CommonMark\Extension\FrontMatter\Data\FrontMatterDataParserInterface;
use League\CommonMark\Extension\FrontMatter\Exception\InvalidFrontMatterException;
use League\CommonMark\Extension\FrontMatter\Input\MarkdownInputWithFrontMatter;
use League\CommonMark\Parser\Cursor;

final class FrontMatterParser implements FrontMatterParserInterface
{
    /** @psalm-readonly */
    private FrontMatterDataParserInterface $frontMatterParser;

    private const REGEX_FRONT_MATTER = '/^---\\R.*?\\R---\\R/s';

    public function __construct(FrontMatterDataParserInterface $frontMatterParser)
    {
        $this->frontMatterParser = $frontMatterParser;
    }

    /**
     * @throws InvalidFrontMatterException if the front matter cannot be parsed
     */
    public function parse(string $markdownContent): MarkdownInputWithFrontMatter
    {
        $cursor = new Cursor($markdownContent);

        // Locate the front matter
        $frontMatter = $cursor->match(self::REGEX_FRONT_MATTER);
        if ($frontMatter === null) {
            return new MarkdownInputWithFrontMatter($markdownContent);
        }

        // Trim the last line (ending ---s and newline)
        $frontMatter = \preg_replace('/---\R$/', '', $frontMatter);
        if ($frontMatter === null) {
            return new MarkdownInputWithFrontMatter($markdownContent);
        }

        // Parse the resulting YAML data
        $data = $this->frontMatterParser->parse($frontMatter);

        // Advance through any remaining newlines which separated the front matter from the Markdown text
        $trailingNewlines = $cursor->match('/^\R+/');

        // Calculate how many lines the Markdown is offset from the front matter by counting the number of newlines
        // Don't forget to add 1 because we stripped one out when trimming the trailing delims
        $lineOffset = \preg_match_all('/\R/', $frontMatter . $trailingNewlines) + 1;

        return new MarkdownInputWithFrontMatter($cursor->getRemainder(), $lineOffset, $data);
    }
}
