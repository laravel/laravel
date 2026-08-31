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

use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggeredSubscriber;
use PHPUnit\Runner\FileDoesNotExistException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestTriggeredPhpDeprecationSubscriber extends Subscriber implements PhpDeprecationTriggeredSubscriber
{
    /**
     * @throws FileDoesNotExistException
     * @throws FileDoesNotHaveLineException
     */
    public function notify(PhpDeprecationTriggered $event): void
    {
        $this->generator()->testTriggeredIssue($event);
    }
}
