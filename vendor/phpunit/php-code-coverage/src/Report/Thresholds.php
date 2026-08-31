<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report;

use SebastianBergmann\CodeCoverage\InvalidArgumentException;

/**
 * @immutable
 */
final class Thresholds
{
    private readonly int $lowUpperBound;
    private readonly int $highLowerBound;

    public static function default(): self
    {
        return new self(50, 90);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function from(int $lowUpperBound, int $highLowerBound): self
    {
        if ($lowUpperBound > $highLowerBound) {
            throw new InvalidArgumentException(
                '$lowUpperBound must not be larger than $highLowerBound',
            );
        }

        return new self($lowUpperBound, $highLowerBound);
    }

    private function __construct(int $lowUpperBound, int $highLowerBound)
    {
        $this->lowUpperBound  = $lowUpperBound;
        $this->highLowerBound = $highLowerBound;
    }

    public function lowUpperBound(): int
    {
        return $this->lowUpperBound;
    }

    public function highLowerBound(): int
    {
        return $this->highLowerBound;
    }
}
