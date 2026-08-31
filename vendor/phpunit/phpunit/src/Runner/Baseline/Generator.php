<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Baseline;

use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\NoticeTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\Runner\FileDoesNotExistException;
use PHPUnit\TextUI\Configuration\Source;
use PHPUnit\TextUI\Configuration\SourceFilter;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Generator
{
    private Baseline $baseline;
    private Source $source;

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    public function __construct(Facade $facade, Source $source)
    {
        $facade->registerSubscribers(
            new TestTriggeredDeprecationSubscriber($this),
            new TestTriggeredNoticeSubscriber($this),
            new TestTriggeredPhpDeprecationSubscriber($this),
            new TestTriggeredPhpNoticeSubscriber($this),
            new TestTriggeredPhpWarningSubscriber($this),
            new TestTriggeredWarningSubscriber($this),
        );

        $this->baseline = new Baseline;
        $this->source   = $source;
    }

    public function baseline(): Baseline
    {
        return $this->baseline;
    }

    /**
     * @throws FileDoesNotExistException
     * @throws FileDoesNotHaveLineException
     */
    public function testTriggeredIssue(DeprecationTriggered|NoticeTriggered|PhpDeprecationTriggered|PhpNoticeTriggered|PhpWarningTriggered|WarningTriggered $event): void
    {
        if ($event->wasSuppressed() && !$this->isSuppressionIgnored($event)) {
            return;
        }

        if ($this->restrict($event) && !SourceFilter::instance()->includes($event->file())) {
            return;
        }

        $this->baseline->add(
            Issue::from(
                $event->file(),
                $event->line(),
                null,
                $event->message(),
            ),
        );
    }

    private function restrict(DeprecationTriggered|NoticeTriggered|PhpDeprecationTriggered|PhpNoticeTriggered|PhpWarningTriggered|WarningTriggered $event): bool
    {
        if ($event instanceof WarningTriggered || $event instanceof PhpWarningTriggered) {
            return $this->source->restrictWarnings();
        }

        if ($event instanceof NoticeTriggered || $event instanceof PhpNoticeTriggered) {
            return $this->source->restrictNotices();
        }

        return $this->source->restrictDeprecations();
    }

    private function isSuppressionIgnored(DeprecationTriggered|NoticeTriggered|PhpDeprecationTriggered|PhpNoticeTriggered|PhpWarningTriggered|WarningTriggered $event): bool
    {
        if ($event instanceof WarningTriggered) {
            return $this->source->ignoreSuppressionOfWarnings();
        }

        if ($event instanceof PhpWarningTriggered) {
            return $this->source->ignoreSuppressionOfPhpWarnings();
        }

        if ($event instanceof PhpNoticeTriggered) {
            return $this->source->ignoreSuppressionOfPhpNotices();
        }

        if ($event instanceof NoticeTriggered) {
            return $this->source->ignoreSuppressionOfNotices();
        }

        if ($event instanceof PhpDeprecationTriggered) {
            return $this->source->ignoreSuppressionOfPhpDeprecations();
        }

        return $this->source->ignoreSuppressionOfDeprecations();
    }
}
