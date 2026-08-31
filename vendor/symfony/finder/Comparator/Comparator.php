<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Comparator;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Comparator
{
    private string $operator;

    public function __construct(
        private string $target,
        string $operator = '==',
    ) {
        if (!\in_array($operator, ['>', '<', '>=', '<=', '==', '!='], true)) {
            throw new \InvalidArgumentException(\sprintf('Invalid operator "%s".', $operator));
        }

        $this->operator = $operator;
    }

    /**
     * Gets the target value.
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * Gets the comparison operator.
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Tests against the target.
     */
    public function test(mixed $test): bool
    {
        return match ($this->operator) {
            '>' => $test > $this->target,
            '>=' => $test >= $this->target,
            '<' => $test < $this->target,
            '<=' => $test <= $this->target,
            '!=' => $test != $this->target,
            default => $test == $this->target,
        };
    }
}
