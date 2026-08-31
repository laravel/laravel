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

use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestPreparedSubscriber extends Subscriber implements PreparedSubscriber
{
    public function notify(Prepared $event): void
    {
        $this->printer()->testPrepared();
    }
}
