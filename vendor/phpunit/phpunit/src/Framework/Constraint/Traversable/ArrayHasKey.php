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

use function array_key_exists;
use function is_array;
use ArrayAccess;
use PHPUnit\Util\Exporter;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class ArrayHasKey extends Constraint
{
    private readonly mixed $key;

    public function __construct(mixed $key)
    {
        $this->key = $key;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return 'has the key ' . Exporter::export($this->key);
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     */
    protected function matches(mixed $other): bool
    {
        if (is_array($other)) {
            return array_key_exists($this->key, $other);
        }

        if ($other instanceof ArrayAccess) {
            return $other->offsetExists($this->key);
        }

        return false;
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     */
    protected function failureDescription(mixed $other): string
    {
        return 'an array ' . $this->toString();
    }
}
