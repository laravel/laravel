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

use const JSON_ERROR_CTRL_CHAR;
use const JSON_ERROR_DEPTH;
use const JSON_ERROR_NONE;
use const JSON_ERROR_STATE_MISMATCH;
use const JSON_ERROR_SYNTAX;
use const JSON_ERROR_UTF8;
use function is_string;
use function json_decode;
use function json_last_error;
use function sprintf;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class IsJson extends Constraint
{
    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return 'is valid JSON';
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     */
    protected function matches(mixed $other): bool
    {
        if (!is_string($other) || $other === '') {
            return false;
        }

        json_decode($other);

        if (json_last_error()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     */
    protected function failureDescription(mixed $other): string
    {
        if (!is_string($other)) {
            return $this->valueToTypeStringFragment($other) . 'is valid JSON';
        }

        if ($other === '') {
            return 'an empty string is valid JSON';
        }

        return sprintf(
            'a string is valid JSON (%s)',
            $this->determineJsonError($other),
        );
    }

    private function determineJsonError(string $json): string
    {
        json_decode($json);

        return match (json_last_error()) {
            JSON_ERROR_NONE           => '',
            JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR      => 'Unexpected control character found',
            JSON_ERROR_SYNTAX         => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8           => 'Malformed UTF-8 characters, possibly incorrectly encoded',
            default                   => 'Unknown error',
        };
    }
}
