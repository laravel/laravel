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

use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

final class BacktickParser implements InlineParserInterface
{
    /**
     * Max bound for backtick code span delimiters.
     *
     * @see https://github.com/commonmark/cmark/commit/8ed5c9d
     */
    private const MAX_BACKTICKS = 1000;

    /** @var \WeakReference<Cursor>|null */
    private ?\WeakReference $lastCursor = null;
    private bool $lastCursorScanned     = false;

    /** @var array<int, int> backtick count => position of known ender */
    private array $seenBackticks = [];

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('`+');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $ticks  = $inlineContext->getFullMatch();
        $cursor = $inlineContext->getCursor();
        $cursor->advanceBy($inlineContext->getFullMatchLength());

        $currentPosition = $cursor->getPosition();
        $previousState   = $cursor->saveState();

        if ($this->findMatchingTicks(\strlen($ticks), $cursor)) {
            $code = $cursor->getSubstring($currentPosition, $cursor->getPosition() - $currentPosition - \strlen($ticks));

            $c = \preg_replace('/\n/m', ' ', $code) ?? '';

            if (
                $c !== '' &&
                $c[0] === ' ' &&
                \substr($c, -1, 1) === ' ' &&
                \preg_match('/[^ ]/', $c)
            ) {
                $c = \substr($c, 1, -1);
            }

            $inlineContext->getContainer()->appendChild(new Code($c));

            return true;
        }

        // If we got here, we didn't match a closing backtick sequence
        $cursor->restoreState($previousState);
        $inlineContext->getContainer()->appendChild(new Text($ticks));

        return true;
    }

    /**
     * Locates the matching closer for a backtick code span.
     *
     * Leverages some caching to avoid traversing the same cursor multiple times when
     * we've already seen all the potential backtick closers.
     *
     * @see https://github.com/commonmark/cmark/commit/8ed5c9d
     *
     * @param int    $openTickLength Number of backticks in the opening sequence
     * @param Cursor $cursor         Cursor to scan
     *
     * @return bool True if a matching closer was found, false otherwise
     */
    private function findMatchingTicks(int $openTickLength, Cursor $cursor): bool
    {
        // Reset the seenBackticks cache if this is a new cursor
        if ($this->lastCursor === null || $this->lastCursor->get() !== $cursor) {
            $this->seenBackticks     = [];
            $this->lastCursor        = \WeakReference::create($cursor);
            $this->lastCursorScanned = false;
        }

        if ($openTickLength > self::MAX_BACKTICKS) {
            return false;
        }

        // Return if we already know there's no closer
        if ($this->lastCursorScanned && isset($this->seenBackticks[$openTickLength]) && $this->seenBackticks[$openTickLength] <= $cursor->getPosition()) {
            return false;
        }

        while ($ticks = $cursor->match('/`{1,' . self::MAX_BACKTICKS . '}/m')) {
            $numTicks = \strlen($ticks);

            // Did we find the closer?
            if ($numTicks === $openTickLength) {
                return true;
            }

            // Store position of closer
            if ($numTicks <= self::MAX_BACKTICKS) {
                $this->seenBackticks[$numTicks] = $cursor->getPosition() - $numTicks;
            }
        }

        // Got through whole input without finding closer
        $this->lastCursorScanned = true;

        return false;
    }
}
