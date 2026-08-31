<?php declare(strict_types=1);
/*
 * This file is part of sebastian/lines-of-code.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\LinesOfCode;

/**
 * @immutable
 */
final readonly class LinesOfCode
{
    /**
     * @var non-negative-int
     */
    private int $linesOfCode;

    /**
     * @var non-negative-int
     */
    private int $commentLinesOfCode;

    /**
     * @var non-negative-int
     */
    private int $nonCommentLinesOfCode;

    /**
     * @var non-negative-int
     */
    private int $logicalLinesOfCode;

    /**
     * @param non-negative-int $linesOfCode
     * @param non-negative-int $commentLinesOfCode
     * @param non-negative-int $nonCommentLinesOfCode
     * @param non-negative-int $logicalLinesOfCode
     *
     * @throws IllogicalValuesException
     * @throws NegativeValueException
     */
    public function __construct(int $linesOfCode, int $commentLinesOfCode, int $nonCommentLinesOfCode, int $logicalLinesOfCode)
    {
        /** @phpstan-ignore smaller.alwaysFalse */
        if ($linesOfCode < 0) {
            throw new NegativeValueException('$linesOfCode must not be negative');
        }

        /** @phpstan-ignore smaller.alwaysFalse */
        if ($commentLinesOfCode < 0) {
            throw new NegativeValueException('$commentLinesOfCode must not be negative');
        }

        /** @phpstan-ignore smaller.alwaysFalse */
        if ($nonCommentLinesOfCode < 0) {
            throw new NegativeValueException('$nonCommentLinesOfCode must not be negative');
        }

        /** @phpstan-ignore smaller.alwaysFalse */
        if ($logicalLinesOfCode < 0) {
            throw new NegativeValueException('$logicalLinesOfCode must not be negative');
        }

        if ($linesOfCode - $commentLinesOfCode !== $nonCommentLinesOfCode) {
            throw new IllogicalValuesException('$linesOfCode !== $commentLinesOfCode + $nonCommentLinesOfCode');
        }

        $this->linesOfCode           = $linesOfCode;
        $this->commentLinesOfCode    = $commentLinesOfCode;
        $this->nonCommentLinesOfCode = $nonCommentLinesOfCode;
        $this->logicalLinesOfCode    = $logicalLinesOfCode;
    }

    /**
     * @return non-negative-int
     */
    public function linesOfCode(): int
    {
        return $this->linesOfCode;
    }

    /**
     * @return non-negative-int
     */
    public function commentLinesOfCode(): int
    {
        return $this->commentLinesOfCode;
    }

    /**
     * @return non-negative-int
     */
    public function nonCommentLinesOfCode(): int
    {
        return $this->nonCommentLinesOfCode;
    }

    /**
     * @return non-negative-int
     */
    public function logicalLinesOfCode(): int
    {
        return $this->logicalLinesOfCode;
    }

    public function plus(self $other): self
    {
        return new self(
            $this->linesOfCode() + $other->linesOfCode(),
            $this->commentLinesOfCode() + $other->commentLinesOfCode(),
            $this->nonCommentLinesOfCode() + $other->nonCommentLinesOfCode(),
            $this->logicalLinesOfCode() + $other->logicalLinesOfCode(),
        );
    }
}
