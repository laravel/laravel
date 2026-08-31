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

use Closure;
use ReflectionFunction;

/**
 * @template CallbackInput of mixed
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class Callback extends Constraint
{
    /**
     * @var callable(CallbackInput): bool
     */
    private readonly mixed $callback;

    /**
     * @param callable(CallbackInput $input): bool $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return 'is accepted by specified callback';
    }

    public function isVariadic(): bool
    {
        return (new ReflectionFunction(Closure::fromCallable($this->callback)))->isVariadic();
    }

    /**
     * Evaluates the constraint for parameter $value. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param CallbackInput $other
     */
    protected function matches(mixed $other): bool
    {
        if ($this->isVariadic()) {
            return ($this->callback)(...$other);
        }

        return ($this->callback)($other);
    }
}
