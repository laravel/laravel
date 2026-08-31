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

use League\CommonMark\Delimiter\Bracket;
use League\CommonMark\Environment\EnvironmentAwareInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\Node\Inline\AbstractWebResource;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Node\Inline\AdjacentTextMerger;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Reference\ReferenceInterface;
use League\CommonMark\Reference\ReferenceMapInterface;
use League\CommonMark\Util\LinkParserHelper;
use League\CommonMark\Util\RegexHelper;

final class CloseBracketParser implements InlineParserInterface, EnvironmentAwareInterface
{
    /** @psalm-readonly-allow-private-mutation */
    private EnvironmentInterface $environment;

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::string(']');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        // Look through stack of delimiters for a [ or !
        $opener = $inlineContext->getDelimiterStack()->getLastBracket();
        if ($opener === null) {
            return false;
        }

        if (! $opener->isImage() && ! $opener->isActive()) {
            // no matched opener; remove from stack
            $inlineContext->getDelimiterStack()->removeBracket();

            return false;
        }

        $cursor = $inlineContext->getCursor();

        $startPos      = $cursor->getPosition();
        $previousState = $cursor->saveState();

        $cursor->advanceBy(1);

        // Check to see if we have a link/image

        // Inline link?
        if ($result = $this->tryParseInlineLinkAndTitle($cursor)) {
            $link = $result;
        } elseif ($link = $this->tryParseReference($cursor, $inlineContext->getReferenceMap(), $opener, $startPos)) {
            $reference = $link;
            $link      = ['url' => $link->getDestination(), 'title' => $link->getTitle()];
        } else {
            // No match; remove this opener from stack
            $inlineContext->getDelimiterStack()->removeBracket();
            $cursor->restoreState($previousState);

            return false;
        }

        $inline = $this->createInline($link['url'], $link['title'], $opener->isImage(), $reference ?? null);
        $opener->getNode()->replaceWith($inline);
        while (($label = $inline->next()) !== null) {
            // Is there a Mention or Link contained within this link?
            // CommonMark does not allow nested links, so we'll restore the original text.
            if ($label instanceof Mention) {
                $label->replaceWith($replacement = new Text($label->getPrefix() . $label->getIdentifier()));
                $inline->appendChild($replacement);
            } elseif ($label instanceof Link) {
                foreach ($label->children() as $child) {
                    $label->insertBefore($child);
                }

                $label->detach();
            } else {
                $inline->appendChild($label);
            }
        }

        // Process delimiters such as emphasis inside link/image
        $delimiterStack = $inlineContext->getDelimiterStack();
        $stackBottom    = $opener->getPosition();
        $delimiterStack->processDelimiters($stackBottom, $this->environment->getDelimiterProcessors());
        $delimiterStack->removeBracket();
        $delimiterStack->removeAll($stackBottom);

        // Merge any adjacent Text nodes together
        AdjacentTextMerger::mergeChildNodes($inline);

        // processEmphasis will remove this and later delimiters.
        // Now, for a link, we also remove earlier link openers (no links in links)
        if (! $opener->isImage()) {
            $inlineContext->getDelimiterStack()->deactivateLinkOpeners();
        }

        return true;
    }

    public function setEnvironment(EnvironmentInterface $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * @return array<string, string>|null
     */
    private function tryParseInlineLinkAndTitle(Cursor $cursor): ?array
    {
        if ($cursor->getCurrentCharacter() !== '(') {
            return null;
        }

        $previousState = $cursor->saveState();

        $cursor->advanceBy(1);
        $cursor->advanceToNextNonSpaceOrNewline();
        if (($dest = LinkParserHelper::parseLinkDestination($cursor)) === null) {
            $cursor->restoreState($previousState);

            return null;
        }

        $cursor->advanceToNextNonSpaceOrNewline();
        $previousCharacter = $cursor->peek(-1);
        // We know from previous lines that we've advanced at least one space so far, so this next call should never be null
        \assert(\is_string($previousCharacter));

        $title = '';
        // make sure there's a space before the title:
        if (\preg_match(RegexHelper::REGEX_WHITESPACE_CHAR, $previousCharacter)) {
            $title = LinkParserHelper::parseLinkTitle($cursor) ?? '';
        }

        $cursor->advanceToNextNonSpaceOrNewline();

        if ($cursor->getCurrentCharacter() !== ')') {
            $cursor->restoreState($previousState);

            return null;
        }

        $cursor->advanceBy(1);

        return ['url' => $dest, 'title' => $title];
    }

    private function tryParseReference(Cursor $cursor, ReferenceMapInterface $referenceMap, Bracket $opener, int $startPos): ?ReferenceInterface
    {
        $savePos     = $cursor->saveState();
        $beforeLabel = $cursor->getPosition();
        $n           = LinkParserHelper::parseLinkLabel($cursor);
        if ($n > 2) {
            $start  = $beforeLabel + 1;
            $length = $n - 2;
        } elseif (! $opener->hasNext()) {
            // Empty or missing second label means to use the first label as the reference.
            // The reference must not contain a bracket. If we know there's a bracket, we don't even bother checking it.
            $start  = $opener->getPosition();
            $length = $startPos - $start;
        } else {
            $cursor->restoreState($savePos);

            return null;
        }

        $referenceLabel = $cursor->getSubstring($start, $length);

        if ($n === 0) {
            // If shortcut reference link, rewind before spaces we skipped
            $cursor->restoreState($savePos);
        }

        return $referenceMap->get($referenceLabel);
    }

    private function createInline(string $url, string $title, bool $isImage, ?ReferenceInterface $reference = null): AbstractWebResource
    {
        if ($isImage) {
            $inline = new Image($url, null, $title);
        } else {
            $inline = new Link($url, null, $title);
        }

        if ($reference) {
            $inline->data->set('reference', $reference);
        }

        return $inline;
    }
}
