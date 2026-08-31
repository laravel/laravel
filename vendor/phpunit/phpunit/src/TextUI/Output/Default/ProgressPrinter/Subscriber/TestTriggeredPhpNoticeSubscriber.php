<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggeredSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestTriggeredPhpNoticeSubscriber extends Subscriber implements PhpNoticeTriggeredSubscriber
{
    public function notify(PhpNoticeTriggered $event): void
    {
        $this->printer()->testTriggeredPhpNotice($event);
    }
}
