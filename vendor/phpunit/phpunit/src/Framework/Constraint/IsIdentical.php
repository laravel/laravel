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

use function explode;
use function gettype;
use function is_array;
use function is_object;
use function is_string;
use function sprintf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
use UnitEnum;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class IsIdentical extends Constraint
{
    private readonly mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Evaluates the constraint for parameter $other.
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @throws ExpectationFailedException
     */
    public function evaluate(mixed $other, string $description = '', bool $returnResult = false): ?bool
    {
        $success = $this->value === $other;

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            $f = null;

            // if both values are strings, make sure a diff is generated
            if (is_string($this->value) && is_string($other)) {
                $f = new ComparisonFailure(
                    $this->value,
                    $other,
                    sprintf("'%s'", $this->value),
                    sprintf("'%s'", $other),
                );
            }

            // if both values are array or enums, make sure a diff is generated
            if ((is_array($this->value) && is_array($other)) || ($this->value instanceof UnitEnum && $other instanceof UnitEnum)) {
                $f = new ComparisonFailure(
                    $this->value,
                    $other,
                    Exporter::export($this->value),
                    Exporter::export($other),
                );
            }

            $this->fail($other, $description, $f);
        }

        return null;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        if (is_object($this->value)) {
            return 'is identical to an object of class "' .
                $this->value::class . '"';
        }

        return 'is identical to ' . Exporter::export($this->value);
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     */
    protected function failureDescription(mixed $other): string
    {
        if (is_object($this->value) && is_object($other)) {
            return 'two variables reference the same object';
        }

        if (explode(' ', gettype($this->value), 2)[0] === 'resource' && explode(' ', gettype($other), 2)[0] === 'resource') {
            return 'two variables reference the same resource';
        }

        if (is_string($this->value) && is_string($other)) {
            return 'two strings are identical';
        }

        if (is_array($this->value) && is_array($other)) {
            return 'two arrays are identical';
        }

        return parent::failureDescription($other);
    }
}
