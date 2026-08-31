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

use function array_map;
use function count;
use function preg_match;
use function preg_quote;
use function preg_replace;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class LogicalNot extends UnaryOperator
{
    public static function negate(string $string): string
    {
        $positives = [
            'contains ',
            'exists',
            'has ',
            'is ',
            'are ',
            'matches ',
            'starts with ',
            'ends with ',
            'reference ',
            'not not ',
        ];

        $negatives = [
            'does not contain ',
            'does not exist',
            'does not have ',
            'is not ',
            'are not ',
            'does not match ',
            'starts not with ',
            'ends not with ',
            'don\'t reference ',
            'not ',
        ];

        preg_match('/(\'[\w\W]*\')([\w\W]*)("[\w\W]*")/i', $string, $matches);

        if (count($matches) === 0) {
            preg_match('/(\'[\w\W]*\')([\w\W]*)(\'[\w\W]*\')/i', $string, $matches);
        }

        $positives = array_map(
            static fn (string $s) => '/\\b' . preg_quote($s, '/') . '/',
            $positives,
        );

        if (count($matches) > 0) {
            $nonInput = $matches[2];

            $negatedString = preg_replace(
                '/' . preg_quote($nonInput, '/') . '/',
                preg_replace(
                    $positives,
                    $negatives,
                    $nonInput,
                ),
                $string,
            );
        } else {
            $negatedString = preg_replace(
                $positives,
                $negatives,
                $string,
            );
        }

        return $negatedString;
    }

    /**
     * Returns the name of this operator.
     */
    public function operator(): string
    {
        return 'not';
    }

    /**
     * Returns this operator's precedence.
     *
     * @see https://www.php.net/manual/en/language.operators.precedence.php
     */
    public function precedence(): int
    {
        return 5;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @throws ExpectationFailedException
     */
    protected function matches(mixed $other): bool
    {
        return !$this->constraint()->evaluate($other, '', true);
    }

    /**
     * Applies additional transformation to strings returned by toString() or
     * failureDescription().
     */
    protected function transformString(string $string): string
    {
        return self::negate($string);
    }

    /**
     * Reduces the sub-expression starting at $this by skipping degenerate
     * sub-expression and returns first descendant constraint that starts
     * a non-reducible sub-expression.
     *
     * See Constraint::reduce() for more.
     */
    protected function reduce(): Constraint
    {
        $constraint = $this->constraint();

        if ($constraint instanceof self) {
            return $constraint->constraint()->reduce();
        }

        return parent::reduce();
    }
}
