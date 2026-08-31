<?php

declare(strict_types=1);

namespace League\CommonMark\Delimiter;

use League\CommonMark\Delimiter\Processor\DelimiterProcessorCollection;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Util\RegexHelper;

/**
 * Delimiter parsing is implemented as an Inline Parser with the lowest-possible priority
 *
 * @internal
 */
final class DelimiterParser implements InlineParserInterface
{
    private DelimiterProcessorCollection $collection;

    public function __construct(DelimiterProcessorCollection $collection)
    {
        $this->collection = $collection;
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::oneOf(...$this->collection->getDelimiterCharacters());
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $character = $inlineContext->getFullMatch();
        $numDelims = 0;
        $cursor    = $inlineContext->getCursor();
        $processor = $this->collection->getDelimiterProcessor($character);

        \assert($processor !== null); // Delimiter processor should never be null here

        $charBefore = $cursor->peek(-1);
        if ($charBefore === null) {
            $charBefore = "\n";
        }

        while ($cursor->peek($numDelims) === $character) {
            ++$numDelims;
        }

        if ($numDelims < $processor->getMinLength()) {
            return false;
        }

        $cursor->advanceBy($numDelims);

        $charAfter = $cursor->getCurrentCharacter();
        if ($charAfter === null) {
            $charAfter = "\n";
        }

        [$canOpen, $canClose] = self::determineCanOpenOrClose($charBefore, $charAfter, $character, $processor);

        if (! ($canOpen || $canClose)) {
            $inlineContext->getContainer()->appendChild(new Text(\str_repeat($character, $numDelims)));

            return true;
        }

        $node = new Text(\str_repeat($character, $numDelims), [
            'delim' => true,
        ]);
        $inlineContext->getContainer()->appendChild($node);

        // Add entry to stack to this opener
        $delimiter = new Delimiter($character, $numDelims, $node, $canOpen, $canClose, $inlineContext->getCursor()->getPosition());
        $inlineContext->getDelimiterStack()->push($delimiter);

        return true;
    }

    /**
     * @return bool[]
     */
    private static function determineCanOpenOrClose(string $charBefore, string $charAfter, string $character, DelimiterProcessorInterface $delimiterProcessor): array
    {
        $afterIsWhitespace   = \preg_match(RegexHelper::REGEX_UNICODE_WHITESPACE_CHAR, $charAfter);
        $afterIsPunctuation  = \preg_match(RegexHelper::REGEX_PUNCTUATION, $charAfter);
        $beforeIsWhitespace  = \preg_match(RegexHelper::REGEX_UNICODE_WHITESPACE_CHAR, $charBefore);
        $beforeIsPunctuation = \preg_match(RegexHelper::REGEX_PUNCTUATION, $charBefore);

        $leftFlanking  = ! $afterIsWhitespace && (! $afterIsPunctuation || $beforeIsWhitespace || $beforeIsPunctuation);
        $rightFlanking = ! $beforeIsWhitespace && (! $beforeIsPunctuation || $afterIsWhitespace || $afterIsPunctuation);

        if ($character === '_') {
            $canOpen  = $leftFlanking && (! $rightFlanking || $beforeIsPunctuation);
            $canClose = $rightFlanking && (! $leftFlanking || $afterIsPunctuation);
        } else {
            $canOpen  = $leftFlanking && $character === $delimiterProcessor->getOpeningCharacter();
            $canClose = $rightFlanking && $character === $delimiterProcessor->getClosingCharacter();
        }

        return [$canOpen, $canClose];
    }
}
