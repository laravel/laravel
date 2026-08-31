<?php

namespace Laravel\Prompts\Support;

/**
 * Result.
 *
 * This is a 'sentinel' value. It wraps a return value, which can
 * allow us to differentiate between a `null` return value and
 * a `null` return value that's intended to continue a loop.
 */
final class Result
{
    public function __construct(public readonly mixed $value)
    {
        //
    }

    public static function from(mixed $value): self
    {
        return new self($value);
    }
}
