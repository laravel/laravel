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

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class TraversableContainsOnly extends Constraint
{
    private readonly Constraint $constraint;
    private readonly string $type;

    /**
     * @param 'array'|'bool'|'boolean'|'callable'|'double'|'float'|'int'|'integer'|'iterable'|'null'|'numeric'|'object'|'real'|'resource (closed)'|'resource'|'scalar'|'string'|class-string $type
     *
     * @throws Exception
     */
    public function __construct(string $type, bool $isNativeType = true)
    {
        if ($isNativeType) {
            $this->constraint = new IsType($type);
        } else {
            $this->constraint = new IsInstanceOf($type);
        }

        $this->type = $type;
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
    public function evaluate(mixed $other, string $description = '', bool $returnResult = false): bool
    {
        $success = true;

        foreach ($other as $item) {
            if (!$this->constraint->evaluate($item, '', true)) {
                $success = false;

                break;
            }
        }

        if (!$success && !$returnResult) {
            $this->fail($other, $description);
        }

        return $success;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return 'contains only values of type "' . $this->type . '"';
    }
}
