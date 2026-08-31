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

use Monolog\LogRecord;

/**
 * Injects value of gethostname in all records
 */
class HostnameProcessor implements ProcessorInterface
{
    private static string $host;

    public function __construct()
    {
        self::$host = (string) gethostname();
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['hostname'] = self::$host;

        return $record;
    }
}
