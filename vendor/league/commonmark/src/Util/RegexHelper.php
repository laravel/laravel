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

namespace League\CommonMark\Util;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;

/**
 * Provides regular expressions and utilities for parsing Markdown
 *
 * All of the PARTIAL_ regex constants assume that they'll be used in case-insensitive searches
 * All other complete regexes provided by this class (either via constants or methods) will have case-insensitivity enabled.
 *
 * @phpcs:disable Generic.Strings.UnnecessaryStringConcat.Found
 *
 * @psalm-immutable
 */
final class RegexHelper
{
    // Partial regular expressions (wrap with `/` on each side and add the case-insensitive `i` flag before use)
    public const PARTIAL_ENTITY                = '&(?>#x[a-f0-9]{1,6}|#[0-9]{1,7}|[a-z][a-z0-9]{1,31});';
    public const PARTIAL_ESCAPABLE             = '[!"#$%&\'()*+,.\/:;<=>?@[\\\\\]^_`{|}~-]';
    public const PARTIAL_ESCAPED_CHAR          = '\\\\' . self::PARTIAL_ESCAPABLE;
    public const PARTIAL_IN_DOUBLE_QUOTES      = '"(' . self::PARTIAL_ESCAPED_CHAR . '|[^"\x00])*"';
    public const PARTIAL_IN_SINGLE_QUOTES      = '\'(' . self::PARTIAL_ESCAPED_CHAR . '|[^\'\x00])*\'';
    public const PARTIAL_IN_PARENS             = '\\((' . self::PARTIAL_ESCAPED_CHAR . '|[^)\x00])*\\)';
    public const PARTIAL_REG_CHAR              = '[^\\\\()\x00-\x20]';
    public const PARTIAL_IN_PARENS_NOSP        = '\((' . self::PARTIAL_REG_CHAR . '|' . self::PARTIAL_ESCAPED_CHAR . '|\\\\)*\)';
    public const PARTIAL_TAGNAME               = '[a-z][a-z0-9-]*';
    public const PARTIAL_BLOCKTAGNAME          = '(?:address|article|aside|base|basefont|blockquote|body|caption|center|col|colgroup|dd|details|dialog|dir|div|dl|dt|fieldset|figcaption|figure|footer|form|frame|frameset|h1|head|header|hr|html|iframe|legend|li|link|main|menu|menuitem|nav|noframes|ol|optgroup|option|p|param|search|section|summary|table|tbody|td|tfoot|th|thead|title|tr|track|ul)';
    public const PARTIAL_ATTRIBUTENAME         = '[a-z_:][a-z0-9:._-]*';
    public const PARTIAL_UNQUOTEDVALUE         = '[^"\'=<>`\x00-\x20]+';
    public const PARTIAL_SINGLEQUOTEDVALUE     = '\'[^\']*\'';
    public const PARTIAL_DOUBLEQUOTEDVALUE     = '"[^"]*"';
    public const PARTIAL_ATTRIBUTEVALUE        = '(?:' . self::PARTIAL_UNQUOTEDVALUE . '|' . self::PARTIAL_SINGLEQUOTEDVALUE . '|' . self::PARTIAL_DOUBLEQUOTEDVALUE . ')';
    public const PARTIAL_ATTRIBUTEVALUESPEC    = '(?:' . '\s*=' . '\s*' . self::PARTIAL_ATTRIBUTEVALUE . ')';
    public const PARTIAL_ATTRIBUTE             = '(?:' . '\s+' . self::PARTIAL_ATTRIBUTENAME . self::PARTIAL_ATTRIBUTEVALUESPEC . '?)';
    public const PARTIAL_OPENTAG               = '<' . self::PARTIAL_TAGNAME . self::PARTIAL_ATTRIBUTE . '*+' . '\s*+\/?+>';
    public const PARTIAL_CLOSETAG              = '<\/' . self::PARTIAL_TAGNAME . '\s*+[>]';
    public const PARTIAL_OPENBLOCKTAG          = '<' . self::PARTIAL_BLOCKTAGNAME . self::PARTIAL_ATTRIBUTE . '*+' . '\s*+\/?+>';
    public const PARTIAL_CLOSEBLOCKTAG         = '<\/' . self::PARTIAL_BLOCKTAGNAME . '\s*+[>]';
    public const PARTIAL_HTMLCOMMENT           = '(?:<!-->|<!--->|<!--[\s\S]*?-->)';
    public const PARTIAL_PROCESSINGINSTRUCTION = '[<][?][\s\S]*?[?][>]';
    public const PARTIAL_DECLARATION           = '<![A-Za-z]+' . '[^>]*>';
    public const PARTIAL_CDATA                 = '<!\[CDATA\[[\s\S]*?]\]>';
    public const PARTIAL_HTMLTAG               = '(?:' . self::PARTIAL_OPENTAG . '|' . self::PARTIAL_CLOSETAG . '|' . self::PARTIAL_HTMLCOMMENT . '|' .
        self::PARTIAL_PROCESSINGINSTRUCTION . '|' . self::PARTIAL_DECLARATION . '|' . self::PARTIAL_CDATA . ')';
    public const PARTIAL_HTMLBLOCKOPEN         = '<(?:' . self::PARTIAL_BLOCKTAGNAME . '(?:[\s\/>]|$)' . '|' .
        '\/' . self::PARTIAL_BLOCKTAGNAME . '(?:[\s>]|$)' . '|' . '[?!])';
    public const PARTIAL_LINK_TITLE            = '^(?:"(' . self::PARTIAL_ESCAPED_CHAR . '|[^"\x00])*+"' .
        '|' . '\'(' . self::PARTIAL_ESCAPED_CHAR . '|[^\'\x00])*+\'' .
        '|' . '\((' . self::PARTIAL_ESCAPED_CHAR . '|[^()\x00])*+\))';

    public const REGEX_PUNCTUATION        = '/^[\p{P}\p{S}]/u';
    public const REGEX_UNSAFE_PROTOCOL    = '/^(?:javascript|vbscript|file|data):/i';
    public const REGEX_SAFE_DATA_PROTOCOL = '/^data:image\/(?:png|gif|jpeg|webp)/i';
    public const REGEX_NON_SPACE          = '/[^ \t\f\v\r\n]/';

    public const REGEX_WHITESPACE_CHAR         = '/^[ \t\n\x0b\x0c\x0d]/';
    public const REGEX_UNICODE_WHITESPACE_CHAR = '/^\pZ|\s/u';
    public const REGEX_THEMATIC_BREAK          = '/^(?:(?:\*[ \t]*){3,}|(?:_[ \t]*){3,}|(?:-[ \t]*){3,})$/';
    public const REGEX_LINK_DESTINATION_BRACES = '/^(?:<(?:[^<>\\n\\\\\\x00]|\\\\.)*>)/';

    /**
     * @psalm-pure
     */
    public static function isEscapable(string $character): bool
    {
        return \preg_match('/' . self::PARTIAL_ESCAPABLE . '/', $character) === 1;
    }

    public static function isWhitespace(string $character): bool
    {
        /** @psalm-suppress InvalidLiteralArgument */
        return $character !== '' && \strpos(" \t\n\x0b\x0c\x0d", $character) !== false;
    }

    /**
     * @psalm-pure
     */
    public static function isLetter(?string $character): bool
    {
        if ($character === null) {
            return false;
        }

        return \preg_match('/[\pL]/u', $character) === 1;
    }

    /**
     * Attempt to match a regex in string s at offset offset
     *
     * @psalm-param non-empty-string $regex
     *
     * @return int|null Index of match, or null
     *
     * @psalm-pure
     */
    public static function matchAt(string $regex, string $string, int $offset = 0): ?int
    {
        $matches = [];
        $string  = \mb_substr($string, $offset, null, 'UTF-8');
        if (! \preg_match($regex, $string, $matches, \PREG_OFFSET_CAPTURE)) {
            return null;
        }

        // PREG_OFFSET_CAPTURE always returns the byte offset, not the char offset, which is annoying
        $charPos = \mb_strlen(\mb_strcut($string, 0, $matches[0][1], 'UTF-8'), 'UTF-8');

        return $offset + $charPos;
    }

    /**
     * Functional wrapper around preg_match_all which only returns the first set of matches
     *
     * @psalm-param non-empty-string $pattern
     *
     * @return string[]|null
     *
     * @psalm-pure
     */
    public static function matchFirst(string $pattern, string $subject, int $offset = 0): ?array
    {
        if ($offset !== 0) {
            $subject = \substr($subject, $offset);
        }

        \preg_match_all($pattern, $subject, $matches, \PREG_SET_ORDER);

        if ($matches === []) {
            return null;
        }

        return $matches[0] ?: null;
    }

    /**
     * Replace backslash escapes with literal characters
     *
     * @psalm-pure
     */
    public static function unescape(string $string): string
    {
        $allEscapedChar = '/\\\\(' . self::PARTIAL_ESCAPABLE . ')/';

        $escaped = \preg_replace($allEscapedChar, '$1', $string);
        \assert(\is_string($escaped));

        return \preg_replace_callback('/' . self::PARTIAL_ENTITY . '/i', static fn ($e) => Html5EntityDecoder::decode($e[0]), $escaped);
    }

    /**
     * @internal
     *
     * @param int $type HTML block type
     *
     * @psalm-param HtmlBlock::TYPE_* $type
     *
     * @phpstan-param HtmlBlock::TYPE_* $type
     *
     * @psalm-return non-empty-string
     *
     * @throws InvalidArgumentException if an invalid type is given
     *
     * @psalm-pure
     */
    public static function getHtmlBlockOpenRegex(int $type): string
    {
        switch ($type) {
            case HtmlBlock::TYPE_1_CODE_CONTAINER:
                return '/^<(?:script|pre|textarea|style)(?:\s|>|$)/i';
            case HtmlBlock::TYPE_2_COMMENT:
                return '/^<!--/';
            case HtmlBlock::TYPE_3:
                return '/^<[?]/';
            case HtmlBlock::TYPE_4:
                return '/^<![A-Z]/i';
            case HtmlBlock::TYPE_5_CDATA:
                return '/^<!\[CDATA\[/i';
            case HtmlBlock::TYPE_6_BLOCK_ELEMENT:
                return '%^</?+(?:address|article|aside|base|basefont|blockquote|body|caption|center|col|colgroup|dd|details|dialog|dir|div|dl|dt|fieldset|figcaption|figure|footer|form|frame|frameset|h[123456]|head|header|hr|html|iframe|legend|li|link|main|menu|menuitem|nav|noframes|ol|optgroup|option|p|param|section|source|summary|table|tbody|td|tfoot|th|thead|title|tr|track|ul)(?:\s++|[/]?+[>]|$)%i';
            case HtmlBlock::TYPE_7_MISC_ELEMENT:
                return '/^(?:' . self::PARTIAL_OPENTAG . '|' . self::PARTIAL_CLOSETAG . ')\\s*$/i';
            default:
                throw new InvalidArgumentException('Invalid HTML block type');
        }
    }

    /**
     * @internal
     *
     * @param int $type HTML block type
     *
     * @psalm-param HtmlBlock::TYPE_* $type
     *
     * @phpstan-param HtmlBlock::TYPE_* $type
     *
     * @psalm-return non-empty-string
     *
     * @throws InvalidArgumentException if an invalid type is given
     *
     * @psalm-pure
     */
    public static function getHtmlBlockCloseRegex(int $type): string
    {
        switch ($type) {
            case HtmlBlock::TYPE_1_CODE_CONTAINER:
                return '%<\/(?:script|pre|textarea|style)>%i';
            case HtmlBlock::TYPE_2_COMMENT:
                return '/-->/';
            case HtmlBlock::TYPE_3:
                return '/\?>/';
            case HtmlBlock::TYPE_4:
                return '/>/';
            case HtmlBlock::TYPE_5_CDATA:
                return '/\]\]>/';
            default:
                throw new InvalidArgumentException('Invalid HTML block type');
        }
    }

    /**
     * @psalm-pure
     */
    public static function isLinkPotentiallyUnsafe(string $url): bool
    {
        return \preg_match(self::REGEX_UNSAFE_PROTOCOL, $url) !== 0 && \preg_match(self::REGEX_SAFE_DATA_PROTOCOL, $url) === 0;
    }
}
