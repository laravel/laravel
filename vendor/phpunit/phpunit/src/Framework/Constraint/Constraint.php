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

use function assert;
use function gettype;
use function is_int;
use function is_object;
use function sprintf;
use function str_replace;
use function strpos;
use function strtolower;
use function substr;
use Countable;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\SelfDescribing;
use PHPUnit\Util\Exporter;
use ReflectionObject;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
abstract class Constraint implements Countable, SelfDescribing
{
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
        $success = false;

        if ($this->matches($other)) {
            $success = true;
        }

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            $this->fail($other, $description);
        }

        return null;
    }

    /**
     * Counts the number of constraint elements.
     */
    public function count(): int
    {
        return 1;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     */
    protected function matches(mixed $other): bool
    {
        return false;
    }

    /**
     * Throws an exception for the given compared value and test description.
     *
     * @throws ExpectationFailedException
     */
    protected function fail(mixed $other, string $description, ?ComparisonFailure $comparisonFailure = null): never
    {
        $failureDescription = sprintf(
            'Failed asserting that %s.',
            $this->failureDescription($other),
        );

        $additionalFailureDescription = $this->additionalFailureDescription($other);

        if ($additionalFailureDescription) {
            $failureDescription .= "\n" . $additionalFailureDescription;
        }

        if (!empty($description)) {
            $failureDescription = $description . "\n" . $failureDescription;
        }

        throw new ExpectationFailedException(
            $failureDescription,
            $comparisonFailure,
        );
    }

    /**
     * Return additional failure description where needed.
     *
     * The function can be overridden to provide additional failure
     * information like a diff
     */
    protected function additionalFailureDescription(mixed $other): string
    {
        return '';
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * To provide additional failure information additionalFailureDescription
     * can be used.
     */
    protected function failureDescription(mixed $other): string
    {
        return Exporter::export($other) . ' ' . $this->toString();
    }

    /**
     * Returns a custom string representation of the constraint object when it
     * appears in context of an $operator expression.
     *
     * The purpose of this method is to provide meaningful descriptive string
     * in context of operators such as LogicalNot. Native PHPUnit constraints
     * are supported out of the box by LogicalNot, but externally developed
     * ones had no way to provide correct strings in this context.
     *
     * The method shall return empty string, when it does not handle
     * customization by itself.
     */
    protected function toStringInContext(Operator $operator, mixed $role): string
    {
        return '';
    }

    /**
     * Returns the description of the failure when this constraint appears in
     * context of an $operator expression.
     *
     * The purpose of this method is to provide meaningful failure description
     * in context of operators such as LogicalNot. Native PHPUnit constraints
     * are supported out of the box by LogicalNot, but externally developed
     * ones had no way to provide correct messages in this context.
     *
     * The method shall return empty string, when it does not handle
     * customization by itself.
     */
    protected function failureDescriptionInContext(Operator $operator, mixed $role, mixed $other): string
    {
        $string = $this->toStringInContext($operator, $role);

        if ($string === '') {
            return '';
        }

        return Exporter::export($other) . ' ' . $string;
    }

    /**
     * Reduces the sub-expression starting at $this by skipping degenerate
     * sub-expression and returns first descendant constraint that starts
     * a non-reducible sub-expression.
     *
     * Returns $this for terminal constraints and for operators that start
     * non-reducible sub-expression, or the nearest descendant of $this that
     * starts a non-reducible sub-expression.
     *
     * A constraint expression may be modelled as a tree with non-terminal
     * nodes (operators) and terminal nodes. For example:
     *
     *      LogicalOr           (operator, non-terminal)
     *      + LogicalAnd        (operator, non-terminal)
     *      | + IsType('int')   (terminal)
     *      | + GreaterThan(10) (terminal)
     *      + LogicalNot        (operator, non-terminal)
     *        + IsType('array') (terminal)
     *
     * A degenerate sub-expression is a part of the tree, that effectively does
     * not contribute to the evaluation of the expression it appears in. An example
     * of degenerate sub-expression is a BinaryOperator constructed with single
     * operand or nested BinaryOperators, each with single operand. An
     * expression involving a degenerate sub-expression is equivalent to a
     * reduced expression with the degenerate sub-expression removed, for example
     *
     *      LogicalAnd          (operator)
     *      + LogicalOr         (degenerate operator)
     *      | + LogicalAnd      (degenerate operator)
     *      |   + IsType('int') (terminal)
     *      + GreaterThan(10)   (terminal)
     *
     * is equivalent to
     *
     *      LogicalAnd          (operator)
     *      + IsType('int')     (terminal)
     *      + GreaterThan(10)   (terminal)
     *
     * because the subexpression
     *
     *      + LogicalOr
     *        + LogicalAnd
     *          + -
     *
     * is degenerate. Calling reduce() on the LogicalOr object above, as well
     * as on LogicalAnd, shall return the IsType('int') instance.
     *
     * Other specific reductions can be implemented, for example cascade of
     * LogicalNot operators
     *
     *      + LogicalNot
     *        + LogicalNot
     *          +LogicalNot
     *           + IsTrue
     *
     * can be reduced to
     *
     *      LogicalNot
     *      + IsTrue
     */
    protected function reduce(): self
    {
        return $this;
    }

    /**
     * @return non-empty-string
     */
    protected function valueToTypeStringFragment(mixed $value): string
    {
        if (is_object($value)) {
            $reflector = new ReflectionObject($value);

            if ($reflector->isAnonymous()) {
                $name = str_replace('class@anonymous', '', $reflector->getName());

                $length = strpos($name, '$');

                assert(is_int($length));

                $name = substr($name, 0, $length);

                return 'an instance of anonymous class created at ' . $name . ' ';
            }

            return 'an instance of class ' . $reflector->getName() . ' ';
        }

        $type = strtolower(gettype($value));

        if ($type === 'double') {
            $type = 'float';
        }

        if ($type === 'resource (closed)') {
            $type = 'closed resource';
        }

        return match ($type) {
            'array', 'integer'                                          => 'an ' . $type . ' ',
            'boolean', 'closed resource', 'float', 'resource', 'string' => 'a ' . $type . ' ',
            'null'                                                      => 'null ',
            default                                                     => 'a value of ' . $type . ' ',
        };
    }
}
