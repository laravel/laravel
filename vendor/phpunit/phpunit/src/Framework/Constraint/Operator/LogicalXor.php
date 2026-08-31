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

use function array_reduce;
use function array_shift;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class LogicalXor extends BinaryOperator
{
    public static function fromConstraints(mixed ...$constraints): self
    {
        return new self(...$constraints);
    }

    /**
     * Returns the name of this operator.
     */
    public function operator(): string
    {
        return 'xor';
    }

    /**
     * Returns this operator's precedence.
     *
     * @see https://www.php.net/manual/en/language.operators.precedence.php.
     */
    public function precedence(): int
    {
        return 23;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @throws ExpectationFailedException
     */
    public function matches(mixed $other): bool
    {
        $constraints = $this->constraints();

        $initial = array_shift($constraints);

        if ($initial === null) {
            return false;
        }

        return array_reduce(
            $constraints,
            static fn (bool $matches, Constraint $constraint): bool => $matches xor $constraint->evaluate($other, '', true),
            $initial->evaluate($other, '', true),
        );
    }
}
