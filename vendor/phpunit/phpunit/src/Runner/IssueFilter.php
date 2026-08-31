<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TestRunner;

use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\ErrorTriggered;
use PHPUnit\Event\Test\NoticeTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\TextUI\Configuration\Source;
use PHPUnit\TextUI\Configuration\SourceFilter;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class IssueFilter
{
    private Source $source;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function shouldBeProcessed(DeprecationTriggered|ErrorTriggered|NoticeTriggered|PhpDeprecationTriggered|PhpNoticeTriggered|PhpWarningTriggered|WarningTriggered $event, bool $onlyTestMethods = false): bool
    {
        if ($onlyTestMethods && !$event->test()->isTestMethod()) {
            return false;
        }

        if ($event instanceof DeprecationTriggered || $event instanceof PhpDeprecationTriggered) {
            if ($event->ignoredByTest()) {
                return false;
            }

            if ($this->source->ignoreSelfDeprecations() && $event->trigger()->isSelf()) {
                return false;
            }

            if ($this->source->ignoreDirectDeprecations() && $event->trigger()->isDirect()) {
                return false;
            }

            if ($this->source->ignoreIndirectDeprecations() && $event->trigger()->isIndirect()) {
                return false;
            }

            if (!$this->source->ignoreSuppressionOfDeprecations() && $event->wasSuppressed()) {
                return false;
            }

            if ($this->source->restrictDeprecations() && !SourceFilter::instance()->includes($event->file())) {
                return false;
            }
        }

        if ($event instanceof NoticeTriggered) {
            if (!$this->source->ignoreSuppressionOfNotices() && $event->wasSuppressed()) {
                return false;
            }

            if ($this->source->restrictNotices() && !SourceFilter::instance()->includes($event->file())) {
                return false;
            }
        }

        if ($event instanceof PhpNoticeTriggered) {
            if (!$this->source->ignoreSuppressionOfPhpNotices() && $event->wasSuppressed()) {
                return false;
            }

            if ($this->source->restrictNotices() && !SourceFilter::instance()->includes($event->file())) {
                return false;
            }
        }

        if ($event instanceof WarningTriggered) {
            if (!$this->source->ignoreSuppressionOfWarnings() && $event->wasSuppressed()) {
                return false;
            }

            if ($this->source->restrictWarnings() && !SourceFilter::instance()->includes($event->file())) {
                return false;
            }
        }

        if ($event instanceof PhpWarningTriggered) {
            if (!$this->source->ignoreSuppressionOfPhpWarnings() && $event->wasSuppressed()) {
                return false;
            }

            if ($this->source->restrictWarnings() && !SourceFilter::instance()->includes($event->file())) {
                return false;
            }
        }

        if ($event instanceof ErrorTriggered) {
            if (!$this->source->ignoreSuppressionOfErrors() && $event->wasSuppressed()) {
                return false;
            }
        }

        return true;
    }
}
