<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Command;

use const PHP_EOL;
use function count;
use function sprintf;
use function str_replace;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\PhptTestCase;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ListTestsAsTextCommand implements Command
{
    /**
     * @var list<PhptTestCase|TestCase>
     */
    private array $tests;

    /**
     * @param list<PhptTestCase|TestCase> $tests
     */
    public function __construct(array $tests)
    {
        $this->tests = $tests;
    }

    public function execute(): Result
    {
        $buffer = sprintf(
            'Available test%s:' . PHP_EOL,
            count($this->tests) > 1 ? 's' : '',
        );

        foreach ($this->tests as $test) {
            if ($test instanceof TestCase) {
                $name = sprintf(
                    '%s::%s',
                    $test::class,
                    str_replace(' with data set ', '', $test->nameWithDataSet()),
                );
            } else {
                $name = $test->getName();
            }

            $buffer .= sprintf(
                ' - %s' . PHP_EOL,
                $name,
            );
        }

        return Result::from($buffer);
    }
}
