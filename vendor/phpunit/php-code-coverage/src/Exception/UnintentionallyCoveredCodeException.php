<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

use function rtrim;
use RuntimeException;

final class UnintentionallyCoveredCodeException extends RuntimeException implements Exception
{
    /**
     * @var list<string>
     */
    private readonly array $unintentionallyCoveredUnits;

    /**
     * @param list<string> $unintentionallyCoveredUnits
     */
    public function __construct(array $unintentionallyCoveredUnits)
    {
        $this->unintentionallyCoveredUnits = $unintentionallyCoveredUnits;

        parent::__construct($this->toString());
    }

    /**
     * @return list<string>
     */
    public function getUnintentionallyCoveredUnits(): array
    {
        return $this->unintentionallyCoveredUnits;
    }

    private function toString(): string
    {
        $message = '';

        foreach ($this->unintentionallyCoveredUnits as $unit) {
            $message .= '- ' . $unit . "\n";
        }

        return rtrim($message);
    }
}
