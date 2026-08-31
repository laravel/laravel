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

use function count;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
abstract class UnaryOperator extends Operator
{
    private readonly Constraint $constraint;

    public function __construct(mixed $constraint)
    {
        $this->constraint = $this->checkConstraint($constraint);
    }

    /**
     * Returns the number of operands (constraints).
     */
    public function arity(): int
    {
        return 1;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        $reduced = $this->reduce();

        if ($reduced !== $this) {
            return $reduced->toString();
        }

        $constraint = $this->constraint->reduce();

        if ($this->constraintNeedsParentheses($constraint)) {
            return $this->operator() . '( ' . $constraint->toString() . ' )';
        }

        $string = $constraint->toStringInContext($this, 0);

        if ($string === '') {
            return $this->transformString($constraint->toString());
        }

        return $string;
    }

    /**
     * Counts the number of constraint elements.
     */
    public function count(): int
    {
        return count($this->constraint);
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     */
    protected function failureDescription(mixed $other): string
    {
        $reduced = $this->reduce();

        if ($reduced !== $this) {
            return $reduced->failureDescription($other);
        }

        $constraint = $this->constraint->reduce();

        if ($this->constraintNeedsParentheses($constraint)) {
            return $this->operator() . '( ' . $constraint->failureDescription($other) . ' )';
        }

        $string = $constraint->failureDescriptionInContext($this, 0, $other);

        if ($string === '') {
            return $this->transformString($constraint->failureDescription($other));
        }

        return $string;
    }

    /**
     * Transforms string returned by the member constraint's toString() or
     * failureDescription() such that it reflects constraint's participation in
     * this expression.
     *
     * The method may be overwritten in a subclass to apply default
     * transformation in case the operand constraint does not provide its own
     * custom strings via toStringInContext() or failureDescriptionInContext().
     */
    protected function transformString(string $string): string
    {
        return $string;
    }

    /**
     * Provides access to $this->constraint for subclasses.
     */
    final protected function constraint(): Constraint
    {
        return $this->constraint;
    }

    /**
     * Returns true if the $constraint needs to be wrapped with parentheses.
     */
    protected function constraintNeedsParentheses(Constraint $constraint): bool
    {
        $constraint = $constraint->reduce();

        return $constraint instanceof self || parent::constraintNeedsParentheses($constraint);
    }
}
