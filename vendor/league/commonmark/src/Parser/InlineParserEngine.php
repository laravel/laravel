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

namespace League\CommonMark\Parser;

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Inline\AdjacentTextMerger;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Reference\ReferenceMapInterface;

/**
 * @internal
 */
final class InlineParserEngine implements InlineParserEngineInterface
{
    /** @psalm-readonly */
    private EnvironmentInterface $environment;

    /** @psalm-readonly */
    private ReferenceMapInterface $referenceMap;

    /**
     * @var array<int, InlineParserInterface|string|bool>
     * @psalm-var list<array{0: InlineParserInterface, 1: non-empty-string, 2: bool}>
     * @phpstan-var array<int, array{0: InlineParserInterface, 1: non-empty-string, 2: bool}>
     */
    private array $parsers = [];

    public function __construct(EnvironmentInterface $environment, ReferenceMapInterface $referenceMap)
    {
        $this->environment  = $environment;
        $this->referenceMap = $referenceMap;

        foreach ($environment->getInlineParsers() as $parser) {
            \assert($parser instanceof InlineParserInterface);
            $regex = $parser->getMatchDefinition()->getRegex();

            $this->parsers[] = [$parser, $regex, \strlen($regex) !== \mb_strlen($regex, 'UTF-8')];
        }
    }

    public function parse(string $contents, AbstractBlock $block): void
    {
        $contents = \trim($contents);
        $cursor   = new Cursor($contents);

        $inlineParserContext = new InlineParserContext($cursor, $block, $this->referenceMap, $this->environment->getConfiguration()->get('max_delimiters_per_line'));

        // Have all parsers look at the line to determine what they might want to parse and what positions they exist at
        foreach ($this->matchParsers($contents) as $matchPosition => $parsers) {
            $currentPosition = $cursor->getPosition();
            // We've already gone past this point
            if ($currentPosition > $matchPosition) {
                continue;
            }

            // We've skipped over some uninteresting text that should be added as a plain text node
            if ($currentPosition < $matchPosition) {
                $cursor->advanceBy($matchPosition - $currentPosition);
                $this->addPlainText($cursor->getPreviousText(), $block);
            }

            // We're now at a potential start - see which of the current parsers can handle it
            $parsed = false;
            foreach ($parsers as [$parser, $matches]) {
                \assert($parser instanceof InlineParserInterface);
                if ($parser->parse($inlineParserContext->withMatches($matches))) {
                    // A parser has successfully handled the text at the given position; don't consider any others at this position
                    $parsed = true;
                    break;
                }
            }

            if ($parsed) {
                continue;
            }

            // Despite potentially being interested, nothing actually parsed text here, so add the current character and continue onwards
            $this->addPlainText((string) $cursor->getCurrentCharacter(), $block);
            $cursor->advance();
        }

        // Add any remaining text that wasn't parsed
        if (! $cursor->isAtEnd()) {
            $this->addPlainText($cursor->getRemainder(), $block);
        }

        // Process any delimiters that were found
        $delimiterStack = $inlineParserContext->getDelimiterStack();
        $delimiterStack->processDelimiters(null, $this->environment->getDelimiterProcessors());
        $delimiterStack->removeAll();

        // Combine adjacent text notes into one
        AdjacentTextMerger::mergeChildNodes($block);
    }

    private function addPlainText(string $text, AbstractBlock $container): void
    {
        $lastInline = $container->lastChild();
        if ($lastInline instanceof Text && ! $lastInline->data->has('delim')) {
            $lastInline->append($text);
        } else {
            $container->appendChild(new Text($text));
        }
    }

    /**
     * Given the current line, ask all the parsers which parts of the text they would be interested in parsing.
     *
     * The resulting array provides a list of character positions, which parsers are interested in trying to parse
     * the text at those points, and (for convenience/optimization) what the matching text happened to be.
     *
     * @return array<array<int, InlineParserInterface|string>>
     *
     * @psalm-return array<int, list<array{0: InlineParserInterface, 1: non-empty-array<string>}>>
     *
     * @phpstan-return array<int, array<int, array{0: InlineParserInterface, 1: non-empty-array<string>}>>
     */
    private function matchParsers(string $contents): array
    {
        $contents    = \trim($contents);
        $isMultibyte = ! \mb_check_encoding($contents, 'ASCII');

        $ret = [];

        foreach ($this->parsers as [$parser, $regex, $isRegexMultibyte]) {
            if ($isMultibyte || $isRegexMultibyte) {
                $regex .= 'u';
            }

            // See if the parser's InlineParserMatch regex matched against any part of the string
            if (! \preg_match_all($regex, $contents, $matches, \PREG_OFFSET_CAPTURE | \PREG_SET_ORDER)) {
                continue;
            }

            // For each part that matched...
            foreach ($matches as $match) {
                if ($isMultibyte) {
                    // PREG_OFFSET_CAPTURE always returns the byte offset, not the char offset, which is annoying
                    $offset = \mb_strlen(\substr($contents, 0, $match[0][1]), 'UTF-8');
                } else {
                    $offset = \intval($match[0][1]);
                }

                // Remove the offsets, keeping only the matched text
                $m = \array_column($match, 0);

                if ($m === []) {
                    continue;
                }

                // Add this match to the list of character positions to stop at
                $ret[$offset][] = [$parser, $m];
            }
        }

        // Sort matches by position so we visit them in order
        \ksort($ret);

        return $ret;
    }
}
