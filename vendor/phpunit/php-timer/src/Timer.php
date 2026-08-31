<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-timer.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Timer;

use function array_pop;
use function hrtime;

final class Timer
{
    /**
     * @var list<float>
     */
    private array $startTimes = [];

    public function start(): void
    {
        $this->startTimes[] = (float) hrtime(true);
    }

    /**
     * @throws NoActiveTimerException
     */
    public function stop(): Duration
    {
        if (empty($this->startTimes)) {
            throw new NoActiveTimerException(
                'Timer::start() has to be called before Timer::stop()',
            );
        }

        return Duration::fromNanoseconds((float) hrtime(true) - array_pop($this->startTimes));
    }
}
