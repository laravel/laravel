<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TestRunner\TestResult;

use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\Test\SkippedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestSkippedSubscriber extends Subscriber implements SkippedSubscriber
{
    public function notify(Skipped $event): void
    {
        $this->collector()->testSkipped($event);
    }
}
