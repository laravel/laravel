<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Output\Default;

use const PHP_EOL;
use function array_keys;
use function array_merge;
use function array_reverse;
use function array_unique;
use function assert;
use function count;
use function explode;
use function ksort;
use function range;
use function sprintf;
use function str_starts_with;
use function strlen;
use function substr;
use function trim;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\AfterLastTestMethodErrored;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\ErrorTriggered;
use PHPUnit\Event\Test\NoticeTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpunitDeprecationTriggered;
use PHPUnit\Event\Test\PhpunitErrorTriggered;
use PHPUnit\Event\Test\PhpunitWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\TestRunner\TestResult\Issues\Issue;
use PHPUnit\TestRunner\TestResult\TestResult;
use PHPUnit\TextUI\Output\Printer;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ResultPrinter
{
    private readonly Printer $printer;
    private readonly bool $displayPhpunitErrors;
    private readonly bool $displayPhpunitWarnings;
    private readonly bool $displayTestsWithErrors;
    private readonly bool $displayTestsWithFailedAssertions;
    private readonly bool $displayRiskyTests;
    private readonly bool $displayPhpunitDeprecations;
    private readonly bool $displayDetailsOnIncompleteTests;
    private readonly bool $displayDetailsOnSkippedTests;
    private readonly bool $displayDetailsOnTestsThatTriggerDeprecations;
    private readonly bool $displayDetailsOnTestsThatTriggerErrors;
    private readonly bool $displayDetailsOnTestsThatTriggerNotices;
    private readonly bool $displayDetailsOnTestsThatTriggerWarnings;
    private readonly bool $displayDefectsInReverseOrder;
    private bool $listPrinted = false;

    public function __construct(Printer $printer, bool $displayPhpunitErrors, bool $displayPhpunitWarnings, bool $displayPhpunitDeprecations, bool $displayTestsWithErrors, bool $displayTestsWithFailedAssertions, bool $displayRiskyTests, bool $displayDetailsOnIncompleteTests, bool $displayDetailsOnSkippedTests, bool $displayDetailsOnTestsThatTriggerDeprecations, bool $displayDetailsOnTestsThatTriggerErrors, bool $displayDetailsOnTestsThatTriggerNotices, bool $displayDetailsOnTestsThatTriggerWarnings, bool $displayDefectsInReverseOrder)
    {
        $this->printer                                      = $printer;
        $this->displayPhpunitErrors                         = $displayPhpunitErrors;
        $this->displayPhpunitWarnings                       = $displayPhpunitWarnings;
        $this->displayPhpunitDeprecations                   = $displayPhpunitDeprecations;
        $this->displayTestsWithErrors                       = $displayTestsWithErrors;
        $this->displayTestsWithFailedAssertions             = $displayTestsWithFailedAssertions;
        $this->displayRiskyTests                            = $displayRiskyTests;
        $this->displayDetailsOnIncompleteTests              = $displayDetailsOnIncompleteTests;
        $this->displayDetailsOnSkippedTests                 = $displayDetailsOnSkippedTests;
        $this->displayDetailsOnTestsThatTriggerDeprecations = $displayDetailsOnTestsThatTriggerDeprecations;
        $this->displayDetailsOnTestsThatTriggerErrors       = $displayDetailsOnTestsThatTriggerErrors;
        $this->displayDetailsOnTestsThatTriggerNotices      = $displayDetailsOnTestsThatTriggerNotices;
        $this->displayDetailsOnTestsThatTriggerWarnings     = $displayDetailsOnTestsThatTriggerWarnings;
        $this->displayDefectsInReverseOrder                 = $displayDefectsInReverseOrder;
    }

    public function print(TestResult $result, bool $stackTraceForDeprecations = false): void
    {
        if ($this->displayPhpunitErrors) {
            $this->printPhpunitErrors($result);
        }

        if ($this->displayPhpunitWarnings) {
            $this->printTestRunnerWarnings($result);
        }

        if ($this->displayPhpunitDeprecations) {
            $this->printTestRunnerDeprecations($result);
        }

        if ($this->displayTestsWithErrors) {
            $this->printTestsWithErrors($result);
        }

        if ($this->displayTestsWithFailedAssertions) {
            $this->printTestsWithFailedAssertions($result);
        }

        if ($this->displayPhpunitWarnings) {
            $this->printDetailsOnTestsThatTriggeredPhpunitWarnings($result);
        }

        if ($this->displayPhpunitDeprecations) {
            $this->printDetailsOnTestsThatTriggeredPhpunitDeprecations($result);
        }

        if ($this->displayRiskyTests) {
            $this->printRiskyTests($result);
        }

        if ($this->displayDetailsOnIncompleteTests) {
            $this->printIncompleteTests($result);
        }

        if ($this->displayDetailsOnSkippedTests) {
            $this->printSkippedTestSuites($result);
            $this->printSkippedTests($result);
        }

        if ($this->displayDetailsOnTestsThatTriggerErrors) {
            $this->printIssueList('error', $result->errors());
        }

        if ($this->displayDetailsOnTestsThatTriggerWarnings) {
            $this->printIssueList('PHP warning', $result->phpWarnings());
            $this->printIssueList('warning', $result->warnings());
        }

        if ($this->displayDetailsOnTestsThatTriggerNotices) {
            $this->printIssueList('PHP notice', $result->phpNotices());
            $this->printIssueList('notice', $result->notices());
        }

        if ($this->displayDetailsOnTestsThatTriggerDeprecations) {
            $this->printIssueList('PHP deprecation', $result->phpDeprecations());
            $this->printIssueList('deprecation', $result->deprecations(), $stackTraceForDeprecations);
        }
    }

    private function printPhpunitErrors(TestResult $result): void
    {
        if (!$result->hasTestTriggeredPhpunitErrorEvents()) {
            return;
        }

        $elements = $this->mapTestsWithIssuesEventsToElements($result->testTriggeredPhpunitErrorEvents());

        $this->printListHeaderWithNumber($elements['numberOfTestsWithIssues'], 'PHPUnit error');
        $this->printList($elements['elements']);
    }

    private function printDetailsOnTestsThatTriggeredPhpunitDeprecations(TestResult $result): void
    {
        if (!$result->hasTestTriggeredPhpunitDeprecationEvents()) {
            return;
        }

        $elements = $this->mapTestsWithIssuesEventsToElements($result->testTriggeredPhpunitDeprecationEvents());

        $this->printListHeaderWithNumberOfTestsAndNumberOfIssues(
            $elements['numberOfTestsWithIssues'],
            $elements['numberOfIssues'],
            'PHPUnit deprecation',
        );

        $this->printList($elements['elements']);
    }

    private function printTestRunnerWarnings(TestResult $result): void
    {
        if (!$result->hasTestRunnerTriggeredWarningEvents()) {
            return;
        }

        $elements = [];
        $messages = [];

        foreach ($result->testRunnerTriggeredWarningEvents() as $event) {
            if (isset($messages[$event->message()])) {
                continue;
            }

            $elements[] = [
                'title' => $event->message(),
                'body'  => '',
            ];

            $messages[$event->message()] = true;
        }

        $this->printListHeaderWithNumber(count($elements), 'PHPUnit test runner warning');
        $this->printList($elements);
    }

    private function printTestRunnerDeprecations(TestResult $result): void
    {
        if (!$result->hasTestRunnerTriggeredDeprecationEvents()) {
            return;
        }

        $elements = [];

        foreach ($result->testRunnerTriggeredDeprecationEvents() as $event) {
            $elements[] = [
                'title' => $event->message(),
                'body'  => '',
            ];
        }

        $this->printListHeaderWithNumber(count($elements), 'PHPUnit test runner deprecation');
        $this->printList($elements);
    }

    private function printDetailsOnTestsThatTriggeredPhpunitWarnings(TestResult $result): void
    {
        if (!$result->hasTestTriggeredPhpunitWarningEvents()) {
            return;
        }

        $elements = $this->mapTestsWithIssuesEventsToElements($result->testTriggeredPhpunitWarningEvents());

        $this->printListHeaderWithNumberOfTestsAndNumberOfIssues(
            $elements['numberOfTestsWithIssues'],
            $elements['numberOfIssues'],
            'PHPUnit warning',
        );

        $this->printList($elements['elements']);
    }

    private function printTestsWithErrors(TestResult $result): void
    {
        if (!$result->hasTestErroredEvents()) {
            return;
        }

        $elements = [];

        foreach ($result->testErroredEvents() as $event) {
            if ($event instanceof AfterLastTestMethodErrored || $event instanceof BeforeFirstTestMethodErrored) {
                $title = $event->testClassName();
            } else {
                $title = $this->name($event->test());
            }

            $elements[] = [
                'title' => $title,
                'body'  => $event->throwable()->asString(),
            ];
        }

        $this->printListHeaderWithNumber(count($elements), 'error');
        $this->printList($elements);
    }

    private function printTestsWithFailedAssertions(TestResult $result): void
    {
        if (!$result->hasTestFailedEvents()) {
            return;
        }

        $elements = [];

        foreach ($result->testFailedEvents() as $event) {
            $body = $event->throwable()->asString();

            if (str_starts_with($body, 'AssertionError: ')) {
                $body = substr($body, strlen('AssertionError: '));
            }

            $elements[] = [
                'title' => $this->name($event->test()),
                'body'  => $body,
            ];
        }

        $this->printListHeaderWithNumber(count($elements), 'failure');
        $this->printList($elements);
    }

    private function printRiskyTests(TestResult $result): void
    {
        if (!$result->hasTestConsideredRiskyEvents()) {
            return;
        }

        $elements = $this->mapTestsWithIssuesEventsToElements($result->testConsideredRiskyEvents());

        $this->printListHeaderWithNumber($elements['numberOfTestsWithIssues'], 'risky test');
        $this->printList($elements['elements']);
    }

    private function printIncompleteTests(TestResult $result): void
    {
        if (!$result->hasTestMarkedIncompleteEvents()) {
            return;
        }

        $elements = [];

        foreach ($result->testMarkedIncompleteEvents() as $event) {
            $elements[] = [
                'title' => $this->name($event->test()),
                'body'  => $event->throwable()->asString(),
            ];
        }

        $this->printListHeaderWithNumber(count($elements), 'incomplete test');
        $this->printList($elements);
    }

    private function printSkippedTestSuites(TestResult $result): void
    {
        if (!$result->hasTestSuiteSkippedEvents()) {
            return;
        }

        $elements = [];

        foreach ($result->testSuiteSkippedEvents() as $event) {
            $elements[] = [
                'title' => $event->testSuite()->name(),
                'body'  => $event->message(),
            ];
        }

        $this->printListHeaderWithNumber(count($elements), 'skipped test suite');
        $this->printList($elements);
    }

    private function printSkippedTests(TestResult $result): void
    {
        if (!$result->hasTestSkippedEvents()) {
            return;
        }

        $elements = [];

        foreach ($result->testSkippedEvents() as $event) {
            $elements[] = [
                'title' => $this->name($event->test()),
                'body'  => $event->message(),
            ];
        }

        $this->printListHeaderWithNumber(count($elements), 'skipped test');
        $this->printList($elements);
    }

    /**
     * @param non-empty-string $type
     * @param list<Issue>      $issues
     */
    private function printIssueList(string $type, array $issues, bool $stackTrace = false): void
    {
        if (empty($issues)) {
            return;
        }

        $numberOfUniqueIssues = count($issues);
        $triggeringTests      = [];

        foreach ($issues as $issue) {
            $triggeringTests = array_merge($triggeringTests, array_keys($issue->triggeringTests()));
        }

        $numberOfTests = count(array_unique($triggeringTests));
        unset($triggeringTests);

        $this->printListHeader(
            sprintf(
                '%d test%s triggered %d %s%s:' . PHP_EOL . PHP_EOL,
                $numberOfTests,
                $numberOfTests !== 1 ? 's' : '',
                $numberOfUniqueIssues,
                $type,
                $numberOfUniqueIssues !== 1 ? 's' : '',
            ),
        );

        $i = 1;

        foreach ($issues as $issue) {
            $title = sprintf(
                '%s:%d',
                $issue->file(),
                $issue->line(),
            );

            $body = trim($issue->description()) . PHP_EOL . PHP_EOL;

            if ($stackTrace && $issue->hasStackTrace()) {
                $body .= trim($issue->stackTrace()) . PHP_EOL . PHP_EOL;
            }

            if (!$issue->triggeredInTest()) {
                $body .= 'Triggered by:';

                $triggeringTests = $issue->triggeringTests();

                ksort($triggeringTests);

                foreach ($triggeringTests as $triggeringTest) {
                    $body .= PHP_EOL . PHP_EOL . '* ' . $triggeringTest['test']->id();

                    if ($triggeringTest['count'] > 1) {
                        $body .= sprintf(
                            ' (%d times)',
                            $triggeringTest['count'],
                        );
                    }

                    if ($triggeringTest['test']->isTestMethod()) {
                        $body .= PHP_EOL . '  ' . $triggeringTest['test']->file() . ':' . $triggeringTest['test']->line();
                    }
                }
            }

            $this->printIssueListElement($i++, $title, $body);

            $this->printer->print(PHP_EOL);
        }
    }

    private function printListHeaderWithNumberOfTestsAndNumberOfIssues(int $numberOfTestsWithIssues, int $numberOfIssues, string $type): void
    {
        $this->printListHeader(
            sprintf(
                "%d test%s triggered %d %s%s:\n\n",
                $numberOfTestsWithIssues,
                $numberOfTestsWithIssues !== 1 ? 's' : '',
                $numberOfIssues,
                $type,
                $numberOfIssues !== 1 ? 's' : '',
            ),
        );
    }

    private function printListHeaderWithNumber(int $number, string $type): void
    {
        $this->printListHeader(
            sprintf(
                "There %s %d %s%s:\n\n",
                ($number === 1) ? 'was' : 'were',
                $number,
                $type,
                ($number === 1) ? '' : 's',
            ),
        );
    }

    private function printListHeader(string $header): void
    {
        if ($this->listPrinted) {
            $this->printer->print("--\n\n");
        }

        $this->listPrinted = true;

        $this->printer->print($header);
    }

    /**
     * @param list<array{title: string, body: string}> $elements
     */
    private function printList(array $elements): void
    {
        $i = 1;

        if ($this->displayDefectsInReverseOrder) {
            $elements = array_reverse($elements);
        }

        foreach ($elements as $element) {
            $this->printListElement($i++, $element['title'], $element['body']);
        }

        $this->printer->print("\n");
    }

    private function printListElement(int $number, string $title, string $body): void
    {
        $body = trim($body);

        $this->printer->print(
            sprintf(
                "%s%d) %s\n%s%s",
                $number > 1 ? "\n" : '',
                $number,
                $title,
                $body,
                !empty($body) ? "\n" : '',
            ),
        );
    }

    private function printIssueListElement(int $number, string $title, string $body): void
    {
        $body = trim($body);

        $this->printer->print(
            sprintf(
                "%d) %s\n%s%s",
                $number,
                $title,
                $body,
                !empty($body) ? "\n" : '',
            ),
        );
    }

    private function name(Test $test): string
    {
        if ($test->isTestMethod()) {
            assert($test instanceof TestMethod);

            if (!$test->testData()->hasDataFromDataProvider()) {
                return $test->nameWithClass();
            }

            return $test->className() . '::' . $test->methodName() . $test->testData()->dataFromDataProvider()->dataAsStringForResultOutput();
        }

        return $test->name();
    }

    /**
     * @param array<string,list<ConsideredRisky|DeprecationTriggered|ErrorTriggered|NoticeTriggered|PhpDeprecationTriggered|PhpNoticeTriggered|PhpunitDeprecationTriggered|PhpunitErrorTriggered|PhpunitWarningTriggered|PhpWarningTriggered|WarningTriggered>> $events
     *
     * @return array{numberOfTestsWithIssues: int, numberOfIssues: int, elements: list<array{title: string, body: string}>}
     */
    private function mapTestsWithIssuesEventsToElements(array $events): array
    {
        $elements = [];
        $issues   = 0;

        foreach ($events as $reasons) {
            $test         = $reasons[0]->test();
            $testLocation = $this->testLocation($test);
            $title        = $this->name($test);
            $body         = '';
            $first        = true;
            $single       = count($reasons) === 1;

            foreach ($reasons as $reason) {
                if ($first) {
                    $first = false;
                } else {
                    $body .= PHP_EOL;
                }

                $body .= $this->reasonMessage($reason, $single);
                $body .= $this->reasonLocation($reason, $single);

                $issues++;
            }

            if (!empty($testLocation)) {
                $body .= $testLocation;
            }

            $elements[] = [
                'title' => $title,
                'body'  => $body,
            ];
        }

        return [
            'numberOfTestsWithIssues' => count($events),
            'numberOfIssues'          => $issues,
            'elements'                => $elements,
        ];
    }

    private function testLocation(Test $test): string
    {
        if (!$test->isTestMethod()) {
            return '';
        }

        assert($test instanceof TestMethod);

        return sprintf(
            '%s%s:%d%s',
            PHP_EOL,
            $test->file(),
            $test->line(),
            PHP_EOL,
        );
    }

    private function reasonMessage(ConsideredRisky|DeprecationTriggered|ErrorTriggered|NoticeTriggered|PhpDeprecationTriggered|PhpNoticeTriggered|PhpunitDeprecationTriggered|PhpunitErrorTriggered|PhpunitWarningTriggered|PhpWarningTriggered|WarningTriggered $reason, bool $single): string
    {
        $message = trim($reason->message());

        if ($single) {
            return $message . PHP_EOL;
        }

        $lines  = explode(PHP_EOL, $message);
        $buffer = '* ' . $lines[0] . PHP_EOL;

        if (count($lines) > 1) {
            foreach (range(1, count($lines) - 1) as $line) {
                $buffer .= '  ' . $lines[$line] . PHP_EOL;
            }
        }

        return $buffer;
    }

    private function reasonLocation(ConsideredRisky|DeprecationTriggered|ErrorTriggered|NoticeTriggered|PhpDeprecationTriggered|PhpNoticeTriggered|PhpunitDeprecationTriggered|PhpunitErrorTriggered|PhpunitWarningTriggered|PhpWarningTriggered|WarningTriggered $reason, bool $single): string
    {
        if (!$reason instanceof DeprecationTriggered &&
            !$reason instanceof PhpDeprecationTriggered &&
            !$reason instanceof ErrorTriggered &&
            !$reason instanceof NoticeTriggered &&
            !$reason instanceof PhpNoticeTriggered &&
            !$reason instanceof WarningTriggered &&
            !$reason instanceof PhpWarningTriggered) {
            return '';
        }

        return sprintf(
            '%s%s:%d%s',
            $single ? '' : '  ',
            $reason->file(),
            $reason->line(),
            PHP_EOL,
        );
    }
}
