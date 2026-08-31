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

use function is_float;
use function memory_get_peak_usage;
use function microtime;
use function sprintf;

final class ResourceUsageFormatter
{
    /**
     * @var array<string,int>
     */
    private const SIZES = [
        'GB' => 1073741824,
        'MB' => 1048576,
        'KB' => 1024,
    ];

    public function resourceUsage(Duration $duration): string
    {
        return sprintf(
            'Time: %s, Memory: %s',
            $duration->asString(),
            $this->bytesToString(memory_get_peak_usage(true)),
        );
    }

    /**
     * @throws TimeSinceStartOfRequestNotAvailableException
     */
    public function resourceUsageSinceStartOfRequest(): string
    {
        if (!isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            throw new TimeSinceStartOfRequestNotAvailableException(
                'Cannot determine time at which the request started because $_SERVER[\'REQUEST_TIME_FLOAT\'] is not available',
            );
        }

        if (!is_float($_SERVER['REQUEST_TIME_FLOAT'])) {
            throw new TimeSinceStartOfRequestNotAvailableException(
                'Cannot determine time at which the request started because $_SERVER[\'REQUEST_TIME_FLOAT\'] is not of type float',
            );
        }

        return $this->resourceUsage(
            Duration::fromMicroseconds(
                (1000000 * (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])),
            ),
        );
    }

    private function bytesToString(int $bytes): string
    {
        foreach (self::SIZES as $unit => $value) {
            if ($bytes >= $value) {
                return sprintf('%.2f %s', $bytes / $value, $unit);
            }
        }

        // @codeCoverageIgnoreStart
        return $bytes . ' byte' . ($bytes !== 1 ? 's' : '');
        // @codeCoverageIgnoreEnd
    }
}
