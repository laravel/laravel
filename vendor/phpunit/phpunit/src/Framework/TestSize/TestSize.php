<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\TestSize;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
abstract readonly class TestSize
{
    public static function unknown(): self
    {
        return new Unknown;
    }

    public static function small(): self
    {
        return new Small;
    }

    public static function medium(): self
    {
        return new Medium;
    }

    public static function large(): self
    {
        return new Large;
    }

    /**
     * @phpstan-assert-if-true Known $this
     */
    public function isKnown(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true Unknown $this
     */
    public function isUnknown(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true Small $this
     */
    public function isSmall(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true Medium $this
     */
    public function isMedium(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true Large $this
     */
    public function isLarge(): bool
    {
        return false;
    }

    abstract public function asString(): string;
}
