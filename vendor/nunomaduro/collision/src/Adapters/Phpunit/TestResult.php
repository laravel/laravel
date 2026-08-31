<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasPrintableTestCaseName;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;

/**
 * @internal
 */
final class TestResult
{
    public const FAIL = 'failed';

    public const SKIPPED = 'skipped';

    public const INCOMPLETE = 'incomplete';

    public const TODO = 'todo';

    public const RISKY = 'risky';

    public const DEPRECATED = 'deprecated';

    public const NOTICE = 'notice';

    public const WARN = 'warnings';

    public const RUNS = 'pending';

    public const PASS = 'passed';

    public string $id;

    public string $testCaseName;

    public string $description;

    public string $type;

    public string $compactIcon;

    public string $icon;

    public string $compactColor;

    public string $color;

    public float $duration;

    public ?Throwable $throwable;

    public string $warning = '';

    public string $warningSource = '';

    public array $context;

    /**
     * Creates a new TestResult instance.
     */
    private function __construct(string $id, string $testCaseName, string $description, string $type, string $icon, string $compactIcon, string $color, string $compactColor, array $context, ?Throwable $throwable = null)
    {
        $this->id = $id;
        $this->testCaseName = $testCaseName;
        $this->description = $description;
        $this->type = $type;
        $this->icon = $icon;
        $this->compactIcon = $compactIcon;
        $this->color = $color;
        $this->compactColor = $compactColor;
        $this->throwable = $throwable;
        $this->context = $context;

        $this->duration = 0.0;

        $asWarning = $this->type === TestResult::WARN
            || $this->type === TestResult::RISKY
            || $this->type === TestResult::SKIPPED
            || $this->type === TestResult::DEPRECATED
            || $this->type === TestResult::NOTICE
            || $this->type === TestResult::INCOMPLETE;

        if ($throwable instanceof Throwable && $asWarning) {
            if (in_array($this->type, [TestResult::DEPRECATED, TestResult::NOTICE])) {
                foreach (explode("\n", $throwable->stackTrace()) as $line) {
                    if (strpos($line, 'vendor/nunomaduro/collision') === false) {
                        $this->warningSource = str_replace(getcwd().'/', '', $line);

                        break;
                    }
                }
            }

            $this->warning .= trim((string) preg_replace("/\r|\n/", ' ', $throwable->message()));

            // pest specific
            $this->warning = str_replace('__pest_evaluable_', '', $this->warning);
            $this->warning = str_replace('This test depends on "P\\', 'This test depends on "', $this->warning);
        }
    }

    /**
     * Sets the telemetry information.
     */
    public function setDuration(float $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * Creates a new test from the given test case.
     */
    public static function fromTestCase(Test $test, string $type, ?Throwable $throwable = null): self
    {
        if (! $test instanceof TestMethod) {
            throw new ShouldNotHappen;
        }

        if (is_subclass_of($test->className(), HasPrintableTestCaseName::class)) {
            $testCaseName = $test->className()::getPrintableTestCaseName();
            $context = method_exists($test->className(), 'getPrintableContext') ? $test->className()::getPrintableContext() : [];
        } else {
            $testCaseName = $test->className();
            $context = [];
        }

        $description = self::makeDescription($test);

        $icon = self::makeIcon($type);

        $compactIcon = self::makeCompactIcon($type);

        $color = self::makeColor($type);

        $compactColor = self::makeCompactColor($type);

        return new self($test->id(), $testCaseName, $description, $type, $icon, $compactIcon, $color, $compactColor, $context, $throwable);
    }

    /**
     * Creates a new test from the given Pest Parallel Test Case.
     */
    public static function fromPestParallelTestCase(Test $test, string $type, ?Throwable $throwable = null): self
    {
        if (! $test instanceof TestMethod) {
            throw new ShouldNotHappen;
        }

        if (is_subclass_of($test->className(), HasPrintableTestCaseName::class)) {
            $testCaseName = $test->className()::getPrintableTestCaseName();
            $description = $test->testDox()->prettifiedMethodName();
        } else {
            $testCaseName = $test->className();
            $description = self::makeDescription($test);
        }

        $icon = self::makeIcon($type);

        $compactIcon = self::makeCompactIcon($type);

        $color = self::makeColor($type);

        $compactColor = self::makeCompactColor($type);

        return new self($test->id(), $testCaseName, $description, $type, $icon, $compactIcon, $color, $compactColor, [], $throwable);
    }

    /**
     * Creates a new test from the given test case.
     */
    public static function fromBeforeFirstTestMethodErrored(BeforeFirstTestMethodErrored $event): self
    {
        if (is_subclass_of($event->testClassName(), HasPrintableTestCaseName::class)) {
            $testCaseName = $event->testClassName()::getPrintableTestCaseName();
        } else {
            $testCaseName = $event->testClassName();
        }

        $description = '';

        $icon = self::makeIcon(self::FAIL);

        $compactIcon = self::makeCompactIcon(self::FAIL);

        $color = self::makeColor(self::FAIL);

        $compactColor = self::makeCompactColor(self::FAIL);

        return new self($testCaseName, $testCaseName, $description, self::FAIL, $icon, $compactIcon, $color, $compactColor, [], $event->throwable());
    }

    /**
     * Get the test case description.
     */
    public static function makeDescription(TestMethod $test): string
    {
        if (is_subclass_of($test->className(), HasPrintableTestCaseName::class)) {
            return $test->className()::getLatestPrintableTestCaseMethodName();
        }

        $name = $test->name();

        // First, lets replace underscore by spaces.
        $name = str_replace('_', ' ', $name);

        // Then, replace upper cases by spaces.
        $name = (string) preg_replace('/([A-Z])/', ' $1', $name);

        // Finally, if it starts with `test`, we remove it.
        $name = (string) preg_replace('/^test/', '', $name);

        // Removes spaces
        $name = trim($name);

        // Lower case everything
        $name = mb_strtolower($name);

        return $name;
    }

    /**
     * Get the test case icon.
     */
    public static function makeIcon(string $type): string
    {
        switch ($type) {
            case self::FAIL:
                return '⨯';
            case self::SKIPPED:
                return '-';
            case self::DEPRECATED:
            case self::WARN:
            case self::RISKY:
            case self::NOTICE:
                return '!';
            case self::INCOMPLETE:
                return '…';
            case self::TODO:
                return '↓';
            case self::RUNS:
                return '•';
            default:
                return '✓';
        }
    }

    /**
     * Get the test case compact icon.
     */
    public static function makeCompactIcon(string $type): string
    {
        switch ($type) {
            case self::FAIL:
                return '⨯';
            case self::SKIPPED:
                return 's';
            case self::DEPRECATED:
            case self::NOTICE:
            case self::WARN:
            case self::RISKY:
                return '!';
            case self::INCOMPLETE:
                return 'i';
            case self::TODO:
                return 't';
            case self::RUNS:
                return '•';
            default:
                return '.';
        }
    }

    /**
     * Get the test case compact color.
     */
    public static function makeCompactColor(string $type): string
    {
        switch ($type) {
            case self::FAIL:
                return 'red';
            case self::DEPRECATED:
            case self::NOTICE:
            case self::SKIPPED:
            case self::INCOMPLETE:
            case self::RISKY:
            case self::WARN:
            case self::RUNS:
                return 'yellow';
            case self::TODO:
                return 'cyan';
            default:
                return 'gray';
        }
    }

    /**
     * Get the test case color.
     */
    public static function makeColor(string $type): string
    {
        switch ($type) {
            case self::TODO:
                return 'cyan';
            case self::FAIL:
                return 'red';
            case self::DEPRECATED:
            case self::NOTICE:
            case self::SKIPPED:
            case self::INCOMPLETE:
            case self::RISKY:
            case self::WARN:
            case self::RUNS:
                return 'yellow';
            default:
                return 'green';
        }
    }
}
