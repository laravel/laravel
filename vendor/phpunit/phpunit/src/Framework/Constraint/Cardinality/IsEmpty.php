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
use function gettype;
use function sprintf;
use function str_starts_with;
use Countable;
use EmptyIterator;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class IsEmpty extends Constraint
{
    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return 'is empty';
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     */
    protected function matches(mixed $other): bool
    {
        if ($other instanceof EmptyIterator) {
            return true;
        }

        if ($other instanceof Countable) {
            return count($other) === 0;
        }

        return empty($other);
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     */
    protected function failureDescription(mixed $other): string
    {
        $type = gettype($other);

        return sprintf(
            '%s %s %s',
            str_starts_with($type, 'a') || str_starts_with($type, 'o') ? 'an' : 'a',
            $type,
            $this->toString(),
        );
    }
}
