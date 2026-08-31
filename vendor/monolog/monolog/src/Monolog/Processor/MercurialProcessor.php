<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Monolog\LogRecord;

/**
 * Injects Hg branch and Hg revision number in all records
 *
 * @author Jonathan A. Schweder <jonathanschweder@gmail.com>
 */
class MercurialProcessor implements ProcessorInterface
{
    private Level $level;
    /** @var array{branch: string, revision: string}|array<never>|null */
    private static $cache = null;

    /**
     * @param int|string|Level $level The minimum logging level at which this Processor will be triggered
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    public function __construct(int|string|Level $level = Level::Debug)
    {
        $this->level = Logger::toMonologLevel($level);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        // return if the level is not high enough
        if ($record->level->isLowerThan($this->level)) {
            return $record;
        }

        $record->extra['hg'] = self::getMercurialInfo();

        return $record;
    }

    /**
     * @return array{branch: string, revision: string}|array<never>
     */
    private static function getMercurialInfo(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $result = explode(' ', trim((string) shell_exec('hg id -nb')));
        if (\count($result) >= 3) {
            return self::$cache = [
                'branch' => $result[1],
                'revision' => $result[2],
            ];
        }
        if (\count($result) === 2) {
            return self::$cache = [
                'branch' => $result[1],
                'revision' => $result[0],
            ];
        }

        return self::$cache = [];
    }
}
