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
 * Additional code based on commonmark-java (https://github.com/commonmark/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Parser;

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Input\MarkdownInput;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\BlockContinueParserWithInlinesInterface;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Block\DocumentBlockParser;
use League\CommonMark\Parser\Block\ParagraphParser;
use League\CommonMark\Reference\MemoryLimitedReferenceMap;
use League\CommonMark\Reference\ReferenceInterface;
use League\CommonMark\Reference\ReferenceMap;

final class MarkdownParser implements MarkdownParserInterface
{
    /** @psalm-readonly */
    private EnvironmentInterface $environment;

    /** @psalm-readonly-allow-private-mutation */
    private int $maxNestingLevel;

    /** @psalm-readonly-allow-private-mutation */
    private ReferenceMap $referenceMap;

    /** @psalm-readonly-allow-private-mutation */
    private int $lineNumber = 0;

    /** @psalm-readonly-allow-private-mutation */
    private Cursor $cursor;

    /**
     * @var array<int, BlockContinueParserInterface>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $activeBlockParsers = [];

    /**
     * @var array<int, BlockContinueParserWithInlinesInterface>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $closedBlockParsers = [];

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    private function initialize(): void
    {
        $this->referenceMap       = new ReferenceMap();
        $this->lineNumber         = 0;
        $this->activeBlockParsers = [];
        $this->closedBlockParsers = [];

        $this->maxNestingLevel = $this->environment->getConfiguration()->get('max_nesting_level');
    }

    /**
     * @throws CommonMarkException
     */
    public function parse(string $input): Document
    {
        $this->initialize();

        $documentParser = new DocumentBlockParser($this->referenceMap);
        $this->activateBlockParser($documentParser);

        $preParsedEvent = new DocumentPreParsedEvent($documentParser->getBlock(), new MarkdownInput($input));
        $this->environment->dispatch($preParsedEvent);
        $markdownInput = $preParsedEvent->getMarkdown();

        foreach ($markdownInput->getLines() as $lineNumber => $line) {
            $this->lineNumber = $lineNumber;
            $this->parseLine($line);
        }

        // finalizeAndProcess
        $this->closeBlockParsers(\count($this->activeBlockParsers), $this->lineNumber);
        $this->processInlines(\strlen($input));

        $this->environment->dispatch(new DocumentParsedEvent($documentParser->getBlock()));

        return $documentParser->getBlock();
    }

    /**
     * Analyze a line of text and update the document appropriately. We parse markdown text by calling this on each
     * line of input, then finalizing the document.
     */
    private function parseLine(string $line): void
    {
        // replace NUL characters for security
        $line = \str_replace("\0", "\u{FFFD}", $line);

        $this->cursor = new Cursor($line);

        $matches = $this->parseBlockContinuation();
        if ($matches === null) {
            return;
        }

        $unmatchedBlocks = \count($this->activeBlockParsers) - $matches;
        $blockParser     = $this->activeBlockParsers[$matches - 1];
        $startedNewBlock = false;

        // Unless last matched container is a code block, try new container starts,
        // adding children to the last matched container:
        $tryBlockStarts = $blockParser->getBlock() instanceof Paragraph || $blockParser->isContainer();
        while ($tryBlockStarts) {
            // this is a little performance optimization
            if ($this->cursor->isBlank()) {
                $this->cursor->advanceToEnd();
                break;
            }

            if ($blockParser->getBlock()->getDepth() >= $this->maxNestingLevel) {
                break;
            }

            $blockStart = $this->findBlockStart($blockParser);
            if ($blockStart === null || $blockStart->isAborting()) {
                $this->cursor->advanceToNextNonSpaceOrTab();
                break;
            }

            if (($state = $blockStart->getCursorState()) !== null) {
                $this->cursor->restoreState($state);
            }

            $startedNewBlock = true;

            // We're starting a new block. If we have any previous blocks that need to be closed, we need to do it now.
            if ($unmatchedBlocks > 0) {
                $this->closeBlockParsers($unmatchedBlocks, $this->lineNumber - 1);
                $unmatchedBlocks = 0;
            }

            $oldBlockLineStart = null;
            if ($blockStart->isReplaceActiveBlockParser()) {
                $oldBlockLineStart = $this->prepareActiveBlockParserForReplacement();
            }

            foreach ($blockStart->getBlockParsers() as $newBlockParser) {
                $blockParser    = $this->addChild($newBlockParser, $oldBlockLineStart);
                $tryBlockStarts = $newBlockParser->isContainer();
            }
        }

        // What remains at the offset is a text line. Add the text to the appropriate block.

        // First check for a lazy paragraph continuation:
        if (! $startedNewBlock && ! $this->cursor->isBlank() && $this->getActiveBlockParser()->canHaveLazyContinuationLines()) {
            $this->getActiveBlockParser()->addLine($this->cursor->getRemainder());
        } else {
            // finalize any blocks not matched
            if ($unmatchedBlocks > 0) {
                $this->closeBlockParsers($unmatchedBlocks, $this->lineNumber - 1);
            }

            if (! $blockParser->isContainer()) {
                $this->getActiveBlockParser()->addLine($this->cursor->getRemainder());
            } elseif (! $this->cursor->isBlank()) {
                $this->addChild(new ParagraphParser());
                $this->getActiveBlockParser()->addLine($this->cursor->getRemainder());
            }
        }
    }

    private function parseBlockContinuation(): ?int
    {
        // For each containing block, try to parse the associated line start.
        // The document will always match, so we can skip the first block parser and start at 1 matches
        $matches = 1;
        for ($i = 1; $i < \count($this->activeBlockParsers); $i++) {
            $blockParser   = $this->activeBlockParsers[$i];
            $blockContinue = $blockParser->tryContinue(clone $this->cursor, $this->getActiveBlockParser());
            if ($blockContinue === null) {
                break;
            }

            if ($blockContinue->isFinalize()) {
                $this->closeBlockParsers(\count($this->activeBlockParsers) - $i, $this->lineNumber);

                return null;
            }

            if (($state = $blockContinue->getCursorState()) !== null) {
                $this->cursor->restoreState($state);
            }

            $matches++;
        }

        return $matches;
    }

    private function findBlockStart(BlockContinueParserInterface $lastMatchedBlockParser): ?BlockStart
    {
        $matchedBlockParser = new MarkdownParserState($this->getActiveBlockParser(), $lastMatchedBlockParser);

        foreach ($this->environment->getBlockStartParsers() as $blockStartParser) {
            \assert($blockStartParser instanceof BlockStartParserInterface);
            if (($result = $blockStartParser->tryStart(clone $this->cursor, $matchedBlockParser)) !== null) {
                return $result;
            }
        }

        return null;
    }

    private function closeBlockParsers(int $count, int $endLineNumber): void
    {
        for ($i = 0; $i < $count; $i++) {
            $blockParser = $this->deactivateBlockParser();
            $this->finalize($blockParser, $endLineNumber);

            // phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
            if ($blockParser instanceof BlockContinueParserWithInlinesInterface) {
                // Remember for inline parsing
                $this->closedBlockParsers[] = $blockParser;
            }
        }
    }

    /**
     * Finalize a block. Close it and do any necessary postprocessing, e.g. creating string_content from strings,
     * setting the 'tight' or 'loose' status of a list, and parsing the beginnings of paragraphs for reference
     * definitions.
     */
    private function finalize(BlockContinueParserInterface $blockParser, int $endLineNumber): void
    {
        if ($blockParser instanceof ParagraphParser) {
            $this->updateReferenceMap($blockParser->getReferences());
        }

        $blockParser->getBlock()->setEndLine($endLineNumber);
        $blockParser->closeBlock();
    }

    /**
     * Walk through a block & children recursively, parsing string content into inline content where appropriate.
     */
    private function processInlines(int $inputSize): void
    {
        $p = new InlineParserEngine($this->environment, new MemoryLimitedReferenceMap($this->referenceMap, $inputSize));

        foreach ($this->closedBlockParsers as $blockParser) {
            $blockParser->parseInlines($p);
        }
    }

    /**
     * Add block of type tag as a child of the tip. If the tip can't accept children, close and finalize it and try
     * its parent, and so on til we find a block that can accept children.
     */
    private function addChild(BlockContinueParserInterface $blockParser, ?int $startLineNumber = null): BlockContinueParserInterface
    {
        $blockParser->getBlock()->setStartLine($startLineNumber ?? $this->lineNumber);

        while (! $this->getActiveBlockParser()->canContain($blockParser->getBlock())) {
            $this->closeBlockParsers(1, ($startLineNumber ?? $this->lineNumber) - 1);
        }

        $this->getActiveBlockParser()->getBlock()->appendChild($blockParser->getBlock());
        $this->activateBlockParser($blockParser);

        return $blockParser;
    }

    private function activateBlockParser(BlockContinueParserInterface $blockParser): void
    {
        $this->activeBlockParsers[] = $blockParser;
    }

    /**
     * @throws ParserLogicException
     */
    private function deactivateBlockParser(): BlockContinueParserInterface
    {
        $popped = \array_pop($this->activeBlockParsers);
        if ($popped === null) {
            throw new ParserLogicException('The last block parser should not be deactivated');
        }

        return $popped;
    }

    /**
     * @return int|null The line number where the old block started
     */
    private function prepareActiveBlockParserForReplacement(): ?int
    {
        // Note that we don't want to parse inlines or finalize this block, as it's getting replaced.
        $old = $this->deactivateBlockParser();

        if ($old instanceof ParagraphParser) {
            $this->updateReferenceMap($old->getReferences());
        }

        $old->getBlock()->detach();

        return $old->getBlock()->getStartLine();
    }

    /**
     * @param ReferenceInterface[] $references
     */
    private function updateReferenceMap(iterable $references): void
    {
        foreach ($references as $reference) {
            if (! $this->referenceMap->contains($reference->getLabel())) {
                $this->referenceMap->add($reference);
            }
        }
    }

    /**
     * @throws ParserLogicException
     */
    public function getActiveBlockParser(): BlockContinueParserInterface
    {
        $active = \end($this->activeBlockParsers);
        if ($active === false) {
            throw new ParserLogicException('No active block parsers are available');
        }

        return $active;
    }
}
