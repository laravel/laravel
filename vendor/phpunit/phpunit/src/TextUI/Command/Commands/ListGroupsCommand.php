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
use function ksort;
use function sprintf;
use function str_starts_with;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\PhptTestCase;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ListGroupsCommand implements Command
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
        /** @var array<non-empty-string, positive-int> $groups */
        $groups = [];

        foreach ($this->tests as $test) {
            if ($test instanceof PhptTestCase) {
                if (!isset($groups['default'])) {
                    $groups['default'] = 1;
                } else {
                    $groups['default']++;
                }

                continue;
            }

            foreach ($test->groups() as $group) {
                if (!isset($groups[$group])) {
                    $groups[$group] = 1;
                } else {
                    $groups[$group]++;
                }
            }
        }

        ksort($groups);

        $buffer = sprintf(
            'Available test group%s:' . PHP_EOL,
            count($groups) > 1 ? 's' : '',
        );

        foreach ($groups as $group => $numberOfTests) {
            if (str_starts_with((string) $group, '__phpunit_')) {
                continue;
            }

            $buffer .= sprintf(
                ' - %s (%d test%s)' . PHP_EOL,
                (string) $group,
                $numberOfTests,
                $numberOfTests > 1 ? 's' : '',
            );
        }

        return Result::from($buffer);
    }
}
