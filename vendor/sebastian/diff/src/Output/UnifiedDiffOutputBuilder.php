<?php declare(strict_types=1);
/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Diff\Output;

use function array_splice;
use function count;
use function fclose;
use function fopen;
use function fwrite;
use function max;
use function min;
use function str_ends_with;
use function stream_get_contents;
use function substr;
use SebastianBergmann\Diff\Differ;

/**
 * Builds a diff string representation in unified diff format in chunks.
 */
final class UnifiedDiffOutputBuilder extends AbstractChunkOutputBuilder
{
    private bool $collapseRanges     = true;
    private int $commonLineThreshold = 6;

    /**
     * @var positive-int
     */
    private int $contextLines = 3;
    private string $header;
    private bool $addLineNumbers;

    public function __construct(string $header = "--- Original\n+++ New\n", bool $addLineNumbers = false)
    {
        $this->header         = $header;
        $this->addLineNumbers = $addLineNumbers;
    }

    public function getDiff(array $diff): string
    {
        $buffer = fopen('php://memory', 'r+b');

        if ('' !== $this->header) {
            fwrite($buffer, $this->header);

            if (!str_ends_with($this->header, "\n")) {
                fwrite($buffer, "\n");
            }
        }

        if (0 !== count($diff)) {
            $this->writeDiffHunks($buffer, $diff);
        }

        $diff = stream_get_contents($buffer, -1, 0);

        fclose($buffer);

        // If the diff is non-empty and last char is not a linebreak: add it.
        // This might happen when both the `from` and `to` do not have a trailing linebreak
        $last = substr($diff, -1);

        return '' !== $diff && "\n" !== $last && "\r" !== $last
            ? $diff . "\n"
            : $diff;
    }

    private function writeDiffHunks($output, array $diff): void
    {
        // detect "No newline at end of file" and insert into `$diff` if needed

        $upperLimit = count($diff);

        if (0 === $diff[$upperLimit - 1][1]) {
            $lc = substr($diff[$upperLimit - 1][0], -1);

            if ("\n" !== $lc) {
                array_splice($diff, $upperLimit, 0, [["\n\\ No newline at end of file\n", Differ::NO_LINE_END_EOF_WARNING]]);
            }
        } else {
            // search back for the last `+` and `-` line,
            // check if it has trailing linebreak, else add a warning under it
            $toFind = [1 => true, 2 => true];

            for ($i = $upperLimit - 1; $i >= 0; $i--) {
                if (isset($toFind[$diff[$i][1]])) {
                    unset($toFind[$diff[$i][1]]);
                    $lc = substr($diff[$i][0], -1);

                    if ("\n" !== $lc) {
                        array_splice($diff, $i + 1, 0, [["\n\\ No newline at end of file\n", Differ::NO_LINE_END_EOF_WARNING]]);
                    }

                    if (!count($toFind)) {
                        break;
                    }
                }
            }
        }

        // write hunks to output buffer

        $cutOff      = max($this->commonLineThreshold, $this->contextLines);
        $hunkCapture = false;
        $sameCount   = $toRange = $fromRange = 0;
        $toStart     = $fromStart = 1;
        $i           = 0;

        /** @var int $i */
        foreach ($diff as $i => $entry) {
            if (0 === $entry[1]) { // same
                if (false === $hunkCapture) {
                    $fromStart++;
                    $toStart++;

                    continue;
                }

                $sameCount++;
                $toRange++;
                $fromRange++;

                if ($sameCount === $cutOff) {
                    $contextStartOffset = ($hunkCapture - $this->contextLines) < 0
                        ? $hunkCapture
                        : $this->contextLines;

                    // note: $contextEndOffset = $this->contextLines;
                    //
                    // because we never go beyond the end of the diff.
                    // with the cutoff/contextlines here the follow is never true;
                    //
                    // if ($i - $cutOff + $this->contextLines + 1 > \count($diff)) {
                    //    $contextEndOffset = count($diff) - 1;
                    // }
                    //
                    // ; that would be true for a trailing incomplete hunk case which is dealt with after this loop

                    $this->writeHunk(
                        $diff,
                        $hunkCapture - $contextStartOffset,
                        $i - $cutOff + $this->contextLines + 1,
                        $fromStart - $contextStartOffset,
                        $fromRange - $cutOff + $contextStartOffset + $this->contextLines,
                        $toStart - $contextStartOffset,
                        $toRange - $cutOff + $contextStartOffset + $this->contextLines,
                        $output,
                    );

                    $fromStart += $fromRange;
                    $toStart   += $toRange;

                    $hunkCapture = false;
                    $sameCount   = $toRange = $fromRange = 0;
                }

                continue;
            }

            $sameCount = 0;

            if ($entry[1] === Differ::NO_LINE_END_EOF_WARNING) {
                continue;
            }

            if (false === $hunkCapture) {
                $hunkCapture = $i;
            }

            if (Differ::ADDED === $entry[1]) {
                $toRange++;
            }

            if (Differ::REMOVED === $entry[1]) {
                $fromRange++;
            }
        }

        if (false === $hunkCapture) {
            return;
        }

        // we end here when cutoff (commonLineThreshold) was not reached, but we were capturing a hunk,
        // do not render hunk till end automatically because the number of context lines might be less than the commonLineThreshold

        $contextStartOffset = $hunkCapture - $this->contextLines < 0
            ? $hunkCapture
            : $this->contextLines;

        // prevent trying to write out more common lines than there are in the diff _and_
        // do not write more than configured through the context lines
        $contextEndOffset = min($sameCount, $this->contextLines);

        $fromRange -= $sameCount;
        $toRange   -= $sameCount;

        $this->writeHunk(
            $diff,
            $hunkCapture - $contextStartOffset,
            $i - $sameCount + $contextEndOffset + 1,
            $fromStart - $contextStartOffset,
            $fromRange + $contextStartOffset + $contextEndOffset,
            $toStart - $contextStartOffset,
            $toRange + $contextStartOffset + $contextEndOffset,
            $output,
        );
    }

    private function writeHunk(
        array $diff,
        int $diffStartIndex,
        int $diffEndIndex,
        int $fromStart,
        int $fromRange,
        int $toStart,
        int $toRange,
        $output
    ): void {
        if ($this->addLineNumbers) {
            fwrite($output, '@@ -' . $fromStart);

            if (!$this->collapseRanges || 1 !== $fromRange) {
                fwrite($output, ',' . $fromRange);
            }

            fwrite($output, ' +' . $toStart);

            if (!$this->collapseRanges || 1 !== $toRange) {
                fwrite($output, ',' . $toRange);
            }

            fwrite($output, " @@\n");
        } else {
            fwrite($output, "@@ @@\n");
        }

        for ($i = $diffStartIndex; $i < $diffEndIndex; $i++) {
            if ($diff[$i][1] === Differ::ADDED) {
                fwrite($output, '+' . $diff[$i][0]);
            } elseif ($diff[$i][1] === Differ::REMOVED) {
                fwrite($output, '-' . $diff[$i][0]);
            } elseif ($diff[$i][1] === Differ::OLD) {
                fwrite($output, ' ' . $diff[$i][0]);
            } elseif ($diff[$i][1] === Differ::NO_LINE_END_EOF_WARNING) {
                fwrite($output, "\n"); // $diff[$i][0]
            } else { /* Not changed (old) Differ::OLD or Warning Differ::DIFF_LINE_END_WARNING */
                fwrite($output, ' ' . $diff[$i][0]);
            }
        }
    }
}
