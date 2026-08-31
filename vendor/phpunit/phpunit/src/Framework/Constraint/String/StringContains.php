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

use function is_string;
use function mb_detect_encoding;
use function mb_stripos;
use function mb_strtolower;
use function sprintf;
use function str_contains;
use function strlen;
use function strtr;
use PHPUnit\Util\Exporter;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class StringContains extends Constraint
{
    private readonly string $needle;
    private readonly bool $ignoreCase;
    private readonly bool $ignoreLineEndings;

    public function __construct(string $needle, bool $ignoreCase = false, bool $ignoreLineEndings = false)
    {
        if ($ignoreLineEndings) {
            $needle = $this->normalizeLineEndings($needle);
        }

        $this->needle            = $needle;
        $this->ignoreCase        = $ignoreCase;
        $this->ignoreLineEndings = $ignoreLineEndings;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        $needle = $this->needle;

        if ($this->ignoreCase) {
            $needle = mb_strtolower($this->needle, 'UTF-8');
        }

        return sprintf(
            'contains "%s" [%s](length: %s)',
            $needle,
            $this->detectedEncoding($needle),
            strlen($needle),
        );
    }

    public function failureDescription(mixed $other): string
    {
        $stringifiedHaystack = Exporter::export($other);
        $haystackEncoding    = $this->detectedEncoding($other);
        $haystackLength      = $this->haystackLength($other);

        $haystackInformation = sprintf(
            '%s [%s](length: %s) ',
            $stringifiedHaystack,
            $haystackEncoding,
            $haystackLength,
        );

        $needleInformation = $this->toString();

        return $haystackInformation . $needleInformation;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     */
    protected function matches(mixed $other): bool
    {
        $haystack = $other;

        if ('' === $this->needle) {
            return true;
        }

        if (!is_string($haystack)) {
            return false;
        }

        if ($this->ignoreLineEndings) {
            $haystack = $this->normalizeLineEndings($haystack);
        }

        if ($this->ignoreCase) {
            /*
             * We must use the multibyte-safe version, so we can accurately compare non-latin uppercase characters with
             * their lowercase equivalents.
             */
            return mb_stripos($haystack, $this->needle, 0, 'UTF-8') !== false;
        }

        /*
         * Use the non-multibyte safe functions to see if the string is contained in $other.
         *
         * This function is very fast, and we don't care about the character position in the string.
         *
         * Additionally, we want this method to be binary safe, so we can check if some binary data is in other binary
         * data.
         */
        return str_contains($haystack, $this->needle);
    }

    private function detectedEncoding(mixed $other): string
    {
        if ($this->ignoreCase) {
            return 'Encoding ignored';
        }

        if (!is_string($other)) {
            return 'Encoding detection failed';
        }

        $detectedEncoding = mb_detect_encoding($other, null, true);

        if ($detectedEncoding === false) {
            return 'Encoding detection failed';
        }

        return $detectedEncoding;
    }

    private function haystackLength(mixed $haystack): int
    {
        if (!is_string($haystack)) {
            return 0;
        }

        if ($this->ignoreLineEndings) {
            $haystack = $this->normalizeLineEndings($haystack);
        }

        return strlen($haystack);
    }

    private function normalizeLineEndings(string $string): string
    {
        return strtr(
            $string,
            [
                "\r\n" => "\n",
                "\r"   => "\n",
            ],
        );
    }
}
