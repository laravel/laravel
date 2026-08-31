<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Logging\TestDox;

use function sprintf;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class PlainTextRenderer
{
    /**
     * @param array<string, TestResultCollection> $tests
     */
    public function render(array $tests): string
    {
        $buffer = '';

        foreach ($tests as $prettifiedClassName => $_tests) {
            $buffer .= $prettifiedClassName . "\n";

            foreach ($this->reduce($_tests) as $prettifiedMethodName => $outcome) {
                $buffer .= sprintf(
                    ' [%s] %s' . "\n",
                    $outcome,
                    $prettifiedMethodName,
                );
            }

            $buffer .= "\n";
        }

        return $buffer;
    }

    /**
     * @return array<string, ' '|'x'>
     */
    private function reduce(TestResultCollection $tests): array
    {
        $result = [];

        foreach ($tests as $test) {
            $prettifiedMethodName = $test->test()->testDox()->prettifiedMethodName();

            $success = true;

            if ($test->status()->isError() ||
                $test->status()->isFailure() ||
                $test->status()->isIncomplete() ||
                $test->status()->isSkipped()) {
                $success = false;
            }

            if (!isset($result[$prettifiedMethodName])) {
                $result[$prettifiedMethodName] = $success ? 'x' : ' ';

                continue;
            }

            if ($success) {
                continue;
            }

            $result[$prettifiedMethodName] = ' ';
        }

        return $result;
    }
}
