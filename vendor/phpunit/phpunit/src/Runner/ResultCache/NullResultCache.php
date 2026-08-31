<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\ResultCache;

use PHPUnit\Framework\TestStatus\TestStatus;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class NullResultCache implements ResultCache
{
    public function setStatus(ResultCacheId $id, TestStatus $status): void
    {
    }

    public function status(ResultCacheId $id): TestStatus
    {
        return TestStatus::unknown();
    }

    public function setTime(ResultCacheId $id, float $time): void
    {
    }

    public function time(ResultCacheId $id): float
    {
        return 0;
    }

    public function load(): void
    {
    }

    public function persist(): void
    {
    }
}
