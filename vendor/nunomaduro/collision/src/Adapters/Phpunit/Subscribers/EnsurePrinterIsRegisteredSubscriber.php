<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use NunoMaduro\Collision\Adapters\Phpunit\Printers\DefaultPrinter;
use NunoMaduro\Collision\Adapters\Phpunit\Printers\ReportablePrinter;
use PHPUnit\Event\Application\Started;
use PHPUnit\Event\Application\StartedSubscriber;
use PHPUnit\Event\Facade;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
use PHPUnit\Event\Test\BeforeFirstTestMethodErroredSubscriber;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\ConsideredRiskySubscriber;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\DeprecationTriggeredSubscriber;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\ErroredSubscriber;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\MarkedIncompleteSubscriber;
use PHPUnit\Event\Test\NoticeTriggered;
use PHPUnit\Event\Test\NoticeTriggeredSubscriber;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PassedSubscriber;
use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggeredSubscriber;
use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggeredSubscriber;
use PHPUnit\Event\Test\PhpunitDeprecationTriggered;
use PHPUnit\Event\Test\PhpunitDeprecationTriggeredSubscriber;
use PHPUnit\Event\Test\PhpunitErrorTriggered;
use PHPUnit\Event\Test\PhpunitErrorTriggeredSubscriber;
use PHPUnit\Event\Test\PhpunitWarningTriggered;
use PHPUnit\Event\Test\PhpunitWarningTriggeredSubscriber;
use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggeredSubscriber;
use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\PreparationStartedSubscriber;
use PHPUnit\Event\Test\PrintedUnexpectedOutput;
use PHPUnit\Event\Test\PrintedUnexpectedOutputSubscriber;
use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\Test\SkippedSubscriber;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\Test\WarningTriggeredSubscriber;
use PHPUnit\Event\TestRunner\Configured;
use PHPUnit\Event\TestRunner\ConfiguredSubscriber;
use PHPUnit\Event\TestRunner\DeprecationTriggered as TestRunnerDeprecationTriggered;
use PHPUnit\Event\TestRunner\DeprecationTriggeredSubscriber as TestRunnerDeprecationTriggeredSubscriber;
use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use PHPUnit\Event\TestRunner\WarningTriggered as TestRunnerWarningTriggered;
use PHPUnit\Event\TestRunner\WarningTriggeredSubscriber as TestRunnerWarningTriggeredSubscriber;
use PHPUnit\Runner\Version;

if (class_exists(Version::class) && (int) Version::series() >= 10) {
    /**
     * @internal
     */
    final class EnsurePrinterIsRegisteredSubscriber implements StartedSubscriber
    {
        /**
         * If this subscriber has been registered on PHPUnit's facade.
         */
        private static bool $registered = false;

        /**
         * Runs the subscriber.
         */
        public function notify(Started $event): void
        {
            if (! isset($_SERVER['COLLISION_PRINTER'])) {
                return;
            }

            $printer = new ReportablePrinter(new DefaultPrinter(true));

            if (isset($_SERVER['COLLISION_PRINTER_COMPACT'])) {
                DefaultPrinter::compact(true);
            }

            if (isset($_SERVER['COLLISION_PRINTER_PROFILE'])) {
                DefaultPrinter::profile(true);
            }

            $subscribers = [
                // Configured
                new class($printer) extends Subscriber implements ConfiguredSubscriber
                {
                    public function notify(Configured $event): void
                    {
                        $this->printer()->setDecorated(
                            $event->configuration()->colors()
                        );
                    }
                },

                // Test
                new class($printer) extends Subscriber implements PrintedUnexpectedOutputSubscriber
                {
                    public function notify(PrintedUnexpectedOutput $event): void
                    {
                        $this->printer()->testPrintedUnexpectedOutput($event);
                    }
                },

                // Test Runner
                new class($printer) extends Subscriber implements ExecutionStartedSubscriber
                {
                    public function notify(ExecutionStarted $event): void
                    {
                        $this->printer()->testRunnerExecutionStarted($event);
                    }
                },

                new class($printer) extends Subscriber implements ExecutionFinishedSubscriber
                {
                    public function notify(ExecutionFinished $event): void
                    {
                        $this->printer()->testRunnerExecutionFinished($event);
                    }
                },

                // Test > Hook Methods

                new class($printer) extends Subscriber implements BeforeFirstTestMethodErroredSubscriber
                {
                    public function notify(BeforeFirstTestMethodErrored $event): void
                    {
                        $this->printer()->testBeforeFirstTestMethodErrored($event);
                    }
                },

                // Test > Lifecycle ...

                new class($printer) extends Subscriber implements FinishedSubscriber
                {
                    public function notify(Finished $event): void
                    {
                        $this->printer()->testFinished($event);
                    }
                },

                new class($printer) extends Subscriber implements PreparationStartedSubscriber
                {
                    public function notify(PreparationStarted $event): void
                    {
                        $this->printer()->testPreparationStarted($event);
                    }
                },

                // Test > Issues ...

                new class($printer) extends Subscriber implements ConsideredRiskySubscriber
                {
                    public function notify(ConsideredRisky $event): void
                    {
                        $this->printer()->testConsideredRisky($event);
                    }
                },

                new class($printer) extends Subscriber implements DeprecationTriggeredSubscriber
                {
                    public function notify(DeprecationTriggered $event): void
                    {
                        $this->printer()->testDeprecationTriggered($event);
                    }
                },

                new class($printer) extends Subscriber implements TestRunnerDeprecationTriggeredSubscriber
                {
                    public function notify(TestRunnerDeprecationTriggered $event): void
                    {
                        $this->printer()->testRunnerDeprecationTriggered($event);
                    }
                },

                new class($printer) extends Subscriber implements TestRunnerWarningTriggeredSubscriber
                {
                    public function notify(TestRunnerWarningTriggered $event): void
                    {
                        $this->printer()->testRunnerWarningTriggered($event);
                    }
                },

                new class($printer) extends Subscriber implements PhpDeprecationTriggeredSubscriber
                {
                    public function notify(PhpDeprecationTriggered $event): void
                    {
                        $this->printer()->testPhpDeprecationTriggered($event);
                    }
                },

                new class($printer) extends Subscriber implements PhpunitDeprecationTriggeredSubscriber
                {
                    public function notify(PhpunitDeprecationTriggered $event): void
                    {
                        $this->printer()->testPhpunitDeprecationTriggered($event);
                    }
                },

                new class($printer) extends Subscriber implements PhpNoticeTriggeredSubscriber
                {
                    public function notify(PhpNoticeTriggered $event): void
                    {
                        $this->printer()->testPhpNoticeTriggered($event);
                    }
                },

                new class($printer) extends Subscriber implements PhpWarningTriggeredSubscriber
                {
                    public function notify(PhpWarningTriggered $event): void
                    {
                        $this->printer()->testPhpWarningTriggered($event);
                    }
                },

                new class($printer) extends Subscriber implements PhpunitWarningTriggeredSubscriber
                {
                    public function notify(PhpunitWarningTriggered $event): void
                    {
                        $this->printer()->testPhpunitWarningTriggered($event);
                    }
                },

                new class($printer) extends Subscriber implements PhpunitErrorTriggeredSubscriber
                {
                    public function notify(PhpunitErrorTriggered $event): void
                    {
                        $this->printer()->testPhpunitErrorTriggered($event);
                    }
                },

                // Test > Outcome ...

                new class($printer) extends Subscriber implements ErroredSubscriber
                {
                    public function notify(Errored $event): void
                    {
                        $this->printer()->testErrored($event);
                    }
                },
                new class($printer) extends Subscriber implements FailedSubscriber
                {
                    public function notify(Failed $event): void
                    {
                        $this->printer()->testFailed($event);
                    }
                },
                new class($printer) extends Subscriber implements MarkedIncompleteSubscriber
                {
                    public function notify(MarkedIncomplete $event): void
                    {
                        $this->printer()->testMarkedIncomplete($event);
                    }
                },

                new class($printer) extends Subscriber implements NoticeTriggeredSubscriber
                {
                    public function notify(NoticeTriggered $event): void
                    {
                        $this->printer()->testNoticeTriggered($event);
                    }
                },

                new class($printer) extends Subscriber implements PassedSubscriber
                {
                    public function notify(Passed $event): void
                    {
                        $this->printer()->testPassed($event);
                    }
                },
                new class($printer) extends Subscriber implements SkippedSubscriber
                {
                    public function notify(Skipped $event): void
                    {
                        $this->printer()->testSkipped($event);
                    }
                },

                new class($printer) extends Subscriber implements WarningTriggeredSubscriber
                {
                    public function notify(WarningTriggered $event): void
                    {
                        $this->printer()->testWarningTriggered($event);
                    }
                },
            ];

            Facade::instance()->registerSubscribers(...$subscribers);
        }

        /**
         * Registers the subscriber on PHPUnit's facade.
         */
        public static function register(): void
        {
            $shouldRegister = self::$registered === false
                && isset($_SERVER['COLLISION_PRINTER']);

            if ($shouldRegister) {
                self::$registered = true;

                Facade::instance()->registerSubscriber(new self);
            }
        }
    }
}
