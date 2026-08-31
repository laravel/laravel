<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasPrintableTestCaseName;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;

/**
 * @internal
 */
final class State
{
    /**
     * The complete test suite tests.
     *
     * @var array<string, TestResult>
     */
    public array $suiteTests = [];

    /**
     * The current test case class.
     */
    public ?string $testCaseName;

    /**
     * The current test case tests.
     *
     * @var array<string, TestResult>
     */
    public array $testCaseTests = [];

    /**
     * The current test case tests.
     *
     * @var array<string, TestResult>
     */
    public array $toBePrintedCaseTests = [];

    /**
     * Header printed.
     */
    public bool $headerPrinted = false;

    /**
     * The state constructor.
     */
    public function __construct()
    {
        $this->testCaseName = '';
    }

    /**
     * Checks if the given test already contains a result.
     */
    public function existsInTestCase(Test $test): bool
    {
        return isset($this->testCaseTests[$test->id()]);
    }

    /**
     * Adds the given test to the State.
     */
    public function add(TestResult $test): void
    {
        $this->testCaseName = $test->testCaseName;

        $levels = array_flip([
            TestResult::PASS,
            TestResult::RUNS,
            TestResult::TODO,
            TestResult::SKIPPED,
            TestResult::WARN,
            TestResult::NOTICE,
            TestResult::DEPRECATED,
            TestResult::RISKY,
            TestResult::INCOMPLETE,
            TestResult::FAIL,
        ]);

        if (isset($this->testCaseTests[$test->id])) {
            $existing = $this->testCaseTests[$test->id];

            if ($levels[$existing->type] >= $levels[$test->type]) {
                return;
            }
        }

        $this->testCaseTests[$test->id] = $test;
        $this->toBePrintedCaseTests[$test->id] = $test;

        $this->suiteTests[$test->id] = $test;
    }

    /**
     * Sets the duration of the given test, and returns the test result.
     */
    public function setDuration(Test $test, float $duration): TestResult
    {
        $result = $this->testCaseTests[$test->id()];

        $result->setDuration($duration);

        return $result;
    }

    /**
     * Gets the test case title.
     */
    public function getTestCaseTitle(): string
    {
        foreach ($this->testCaseTests as $test) {
            if ($test->type === TestResult::FAIL) {
                return 'FAIL';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type !== TestResult::PASS && $test->type !== TestResult::TODO && $test->type !== TestResult::DEPRECATED && $test->type !== TestResult::NOTICE) {
                return 'WARN';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type === TestResult::NOTICE) {
                return 'NOTI';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type === TestResult::DEPRECATED) {
                return 'DEPR';
            }
        }

        if ($this->todosCount() > 0 && (count($this->testCaseTests) === $this->todosCount())) {
            return 'TODO';
        }

        return 'PASS';
    }

    /**
     * Gets the number of tests that are todos.
     */
    public function todosCount(): int
    {
        return count(array_values(array_filter($this->testCaseTests, function (TestResult $test): bool {
            return $test->type === TestResult::TODO;
        })));
    }

    /**
     * Gets the test case title color.
     */
    public function getTestCaseFontColor(): string
    {
        if ($this->getTestCaseTitleColor() === 'blue') {
            return 'white';
        }

        return $this->getTestCaseTitle() === 'FAIL' ? 'default' : 'black';
    }

    /**
     * Gets the test case title color.
     */
    public function getTestCaseTitleColor(): string
    {
        foreach ($this->testCaseTests as $test) {
            if ($test->type === TestResult::FAIL) {
                return 'red';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type !== TestResult::PASS && $test->type !== TestResult::TODO && $test->type !== TestResult::DEPRECATED) {
                return 'yellow';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type === TestResult::DEPRECATED) {
                return 'yellow';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type === TestResult::TODO) {
                return 'blue';
            }
        }

        return 'green';
    }

    /**
     * Returns the number of tests on the current test case.
     */
    public function testCaseTestsCount(): int
    {
        return count($this->testCaseTests);
    }

    /**
     * Returns the number of tests on the complete test suite.
     */
    public function testSuiteTestsCount(): int
    {
        return count($this->suiteTests);
    }

    /**
     * Checks if the given test case is different from the current one.
     */
    public function testCaseHasChanged(TestMethod $test): bool
    {
        return self::getPrintableTestCaseName($test) !== $this->testCaseName;
    }

    /**
     * Moves the an new test case.
     */
    public function moveTo(TestMethod $test): void
    {
        $this->testCaseName = self::getPrintableTestCaseName($test);

        $this->testCaseTests = [];

        $this->headerPrinted = false;
    }

    /**
     * Foreach test in the test case.
     */
    public function eachTestCaseTests(callable $callback): void
    {
        foreach ($this->toBePrintedCaseTests as $test) {
            $callback($test);
        }

        $this->toBePrintedCaseTests = [];
    }

    public function countTestsInTestSuiteBy(string $type): int
    {
        return count(array_filter($this->suiteTests, function (TestResult $testResult) use ($type) {
            return $testResult->type === $type;
        }));
    }

    /**
     * Returns the printable test case name from the given `TestCase`.
     */
    public static function getPrintableTestCaseName(TestMethod $test): string
    {
        $className = explode('::', $test->id())[0];

        if (is_subclass_of($className, HasPrintableTestCaseName::class)) {
            return $className::getPrintableTestCaseName();
        }

        return $className;
    }
}
