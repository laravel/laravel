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

use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggeredSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestTriggeredPhpWarningSubscriber extends Subscriber implements PhpWarningTriggeredSubscriber
{
    public function notify(PhpWarningTriggered $event): void
    {
        $this->collector()->testTriggeredPhpWarning($event);
    }
}
