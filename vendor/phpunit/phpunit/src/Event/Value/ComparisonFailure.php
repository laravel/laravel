<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Code;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ComparisonFailure
{
    private string $expected;
    private string $actual;
    private string $diff;

    public function __construct(string $expected, string $actual, string $diff)
    {
        $this->expected = $expected;
        $this->actual   = $actual;
        $this->diff     = $diff;
    }

    public function expected(): string
    {
        return $this->expected;
    }

    public function actual(): string
    {
        return $this->actual;
    }

    public function diff(): string
    {
        return $this->diff;
    }
}
