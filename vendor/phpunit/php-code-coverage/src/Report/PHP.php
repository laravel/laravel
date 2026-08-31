<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report;

use const PHP_EOL;
use function serialize;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\WriteOperationFailedException;
use SebastianBergmann\CodeCoverage\Util\Filesystem;

final class PHP
{
    /**
     * @param null|non-empty-string $target
     *
     * @throws WriteOperationFailedException
     */
    public function process(CodeCoverage $coverage, ?string $target = null): string
    {
        $coverage->clearCache();

        $buffer = "<?php
return \unserialize(<<<'END_OF_COVERAGE_SERIALIZATION'" . PHP_EOL . serialize($coverage) . PHP_EOL . 'END_OF_COVERAGE_SERIALIZATION' . PHP_EOL . ');';

        if ($target !== null) {
            Filesystem::write($target, $buffer);
        }

        return $buffer;
    }
}
