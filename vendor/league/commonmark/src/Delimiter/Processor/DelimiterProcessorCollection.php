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
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Delimiter\Processor;

use League\CommonMark\Exception\InvalidArgumentException;

final class DelimiterProcessorCollection implements DelimiterProcessorCollectionInterface
{
    /**
     * @var array<string,DelimiterProcessorInterface>|DelimiterProcessorInterface[]
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $processorsByChar = [];

    public function add(DelimiterProcessorInterface $processor): void
    {
        $opening = $processor->getOpeningCharacter();
        $closing = $processor->getClosingCharacter();

        if ($opening === $closing) {
            $old = $this->processorsByChar[$opening] ?? null;
            if ($old !== null && $old->getOpeningCharacter() === $old->getClosingCharacter()) {
                $this->addStaggeredDelimiterProcessorForChar($opening, $old, $processor);
            } else {
                $this->addDelimiterProcessorForChar($opening, $processor);
            }
        } else {
            $this->addDelimiterProcessorForChar($opening, $processor);
            $this->addDelimiterProcessorForChar($closing, $processor);
        }
    }

    public function getDelimiterProcessor(string $char): ?DelimiterProcessorInterface
    {
        return $this->processorsByChar[$char] ?? null;
    }

    /**
     * @return string[]
     */
    public function getDelimiterCharacters(): array
    {
        return \array_keys($this->processorsByChar);
    }

    private function addDelimiterProcessorForChar(string $delimiterChar, DelimiterProcessorInterface $processor): void
    {
        if (isset($this->processorsByChar[$delimiterChar])) {
            throw new InvalidArgumentException(\sprintf('Delim processor for character "%s" already exists', $processor->getOpeningCharacter()));
        }

        $this->processorsByChar[$delimiterChar] = $processor;
    }

    private function addStaggeredDelimiterProcessorForChar(string $opening, DelimiterProcessorInterface $old, DelimiterProcessorInterface $new): void
    {
        if ($old instanceof StaggeredDelimiterProcessor) {
            $s = $old;
        } else {
            $s = new StaggeredDelimiterProcessor($opening, $old);
        }

        $s->add($new);
        $this->processorsByChar[$opening] = $s;
    }

    public function count(): int
    {
        return \count($this->processorsByChar);
    }
}
