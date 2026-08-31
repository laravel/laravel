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
use function array_unique;
use function assert;
use function sprintf;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\PhptTestCase;
use ReflectionClass;
use ReflectionException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ListTestFilesCommand implements Command
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

    /**
     * @throws ReflectionException
     */
    public function execute(): Result
    {
        $buffer = 'Available test files:' . PHP_EOL;

        $results = [];

        foreach ($this->tests as $test) {
            if ($test instanceof TestCase) {
                $name = (new ReflectionClass($test))->getFileName();

                assert($name !== false);

                $results[] = $name;

                continue;
            }

            $results[] = $test->getName();
        }

        foreach (array_unique($results) as $result) {
            $buffer .= sprintf(
                ' - %s' . PHP_EOL,
                $result,
            );
        }

        return Result::from($buffer);
    }
}
