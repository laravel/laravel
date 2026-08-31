<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint;

use PHPUnit\Util\Exporter;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class LessThan extends Constraint
{
    private readonly mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return 'is less than ' . Exporter::export($this->value);
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     */
    protected function matches(mixed $other): bool
    {
        return $this->value > $other;
    }
}
